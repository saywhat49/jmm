<#
.SYNOPSIS
    Construit le paquet d'installation JMM, publie le code sur GitHub,
    cree la release et met a jour update/jmm_update.xml avec le bon SHA-256.

.DESCRIPTION
    Sequence :
      1. Lit la version dans jmm.xml.
      2. Prepare une copie propre du composant et la compresse en
         com_jmm-<version>.zip dans dist/.
      3. Calcule le SHA-256 de CE fichier precis.
      4. Genere update/jmm_update.xml.
      5. Commit, push, tag et release GitHub avec l'archive en piece jointe.

    L'ordre compte : le hash doit etre celui de l'archive reellement
    televersee. Recompresser les memes fichiers produit des octets
    differents (horodatages), donc un hash different, et Joomla refuserait
    la mise a jour avec un message de checksum invalide.

    -WhatIf effectue reellement la construction, le hachage et la
    verification du paquet (operations locales et sans effet de bord),
    mais n'ecrit rien dans le depot et ne publie rien.

.PARAMETER SourcePath
    Dossier contenant les sources du composant (jmm.xml a la racine).
    Par defaut, le dossier du script.

.PARAMETER RepoPath
    Copie de travail locale du depot GitHub. Par defaut, identique a
    -SourcePath : c'est le cas ou le depot contient directement les
    sources, sans dossier intermediaire.

.PARAMETER Version
    Version a publier. Par defaut, celle lue dans jmm.xml.

.PARAMETER Branch
    Branche cible. Par defaut master, conformement a l'URL du serveur
    de mise a jour declaree dans jmm.xml.

.PARAMETER SkipRelease
    Prepare et pousse tout, mais ne cree pas la release GitHub.

.EXAMPLE
    .\Deploy-Jmm.ps1 -WhatIf

.EXAMPLE
    .\Deploy-Jmm.ps1

.EXAMPLE
    .\Deploy-Jmm.ps1 -SourcePath C:\dev\jmm-src -RepoPath C:\dev\jmm
#>

#Requires -Version 5.1

[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [string] $SourcePath,

    [string] $RepoPath,

    [string] $Version,

    [string] $Branch = 'master',

    [string] $RepoSlug = 'saywhat49/jmm',

    [switch] $SkipRelease
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Ce qui vit dans le depot mais ne doit PAS etre installe par Joomla.
$ExcludeFromPackage = @(
    '.git', '.github', '.gitignore', '.vscode', '.editorconfig',
    'update', 'dist', 'README.md', 'Deploy-Jmm.ps1'
)

function Write-Step {
    param([string] $Message)
    Write-Host ''
    Write-Host "==> $Message" -ForegroundColor Cyan
}

function Assert-Command {
    param([string] $Name, [string] $Hint)
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "'$Name' est introuvable dans le PATH. $Hint"
    }
}

function Get-Sha256 {
    param([Parameter(Mandatory = $true)][string] $Path)

    $algorithm = [System.Security.Cryptography.SHA256]::Create()
    try {
        $stream = [System.IO.File]::OpenRead($Path)
        try {
            $bytes = $algorithm.ComputeHash($stream)
        }
        finally {
            $stream.Dispose()
        }
    }
    finally {
        $algorithm.Dispose()
    }

    if ($null -eq $bytes -or $bytes.Length -ne 32) {
        throw "Calcul de l'empreinte impossible pour $Path."
    }

    return ([System.BitConverter]::ToString($bytes) -replace '-', '').ToLowerInvariant()
}

function Invoke-Native {
    <#
        Appelle un executable externe sans se faire piegier par
        $ErrorActionPreference = 'Stop'. PowerShell transforme toute sortie
        sur stderr d'un programme natif en erreur terminante, or git et gh
        y ecrivent leur fonctionnement normal : "Switched to branch",
        la progression d'un push, ou "release not found".
        On neutralise la preference le temps de l'appel, on fusionne stderr
        dans la sortie standard, et on decide sur le code de retour, qui est
        le seul indicateur fiable.
    #>
    param(
        [Parameter(Mandatory = $true)][string]   $FilePath,
        [Parameter(Mandatory = $true)][string[]] $Arguments,
        [switch] $AllowFailure
    )

    $previous = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'

    try {
        $output = & $FilePath @Arguments 2>&1 | ForEach-Object { [string] $_ }
        $code   = $LASTEXITCODE
    }
    finally {
        $ErrorActionPreference = $previous
    }

    if (-not $AllowFailure -and $code -ne 0) {
        $joined = ($output -join [Environment]::NewLine)
        throw "$FilePath $($Arguments -join ' ') a echoue (code $code) :$([Environment]::NewLine)$joined"
    }

    return [pscustomobject]@{
        ExitCode = $code
        Output   = @($output)
    }
}

Write-Host 'Deploy-Jmm.ps1 - revision 6' -ForegroundColor DarkGray

# ---------------------------------------------------------------------------
# 1. Environnement
# ---------------------------------------------------------------------------

Write-Step 'Verification de l''environnement'

Assert-Command -Name 'git' -Hint 'Installez Git pour Windows : https://git-scm.com/download/win'

if (-not $SkipRelease) {
    Assert-Command -Name 'gh' -Hint 'Installez GitHub CLI : winget install GitHub.cli, puis "gh auth login".'
}

if (-not $SourcePath) {
    $SourcePath = $PSScriptRoot
}

$SourcePath = (Resolve-Path -LiteralPath $SourcePath).Path

if (-not $RepoPath) {
    $RepoPath = $SourcePath
}

$RepoPath = (Resolve-Path -LiteralPath $RepoPath).Path

# Depot et sources confondus : c'est le cas le plus courant. Il n'y a alors
# rien a synchroniser, et surtout rien a effacer.
$inPlace = ($SourcePath -eq $RepoPath)

$manifestPath = Join-Path $SourcePath 'jmm.xml'
if (-not (Test-Path -LiteralPath $manifestPath)) {
    throw "jmm.xml introuvable dans $SourcePath. -SourcePath doit pointer sur la racine du composant."
}

if (-not (Test-Path -LiteralPath (Join-Path $RepoPath '.git'))) {
    throw "$RepoPath n'est pas une copie de travail Git. Clonez d'abord : git clone https://github.com/$RepoSlug.git"
}

Write-Host "    Sources : $SourcePath"
Write-Host "    Depot   : $RepoPath$(if ($inPlace) { '  (identiques)' })"

# ---------------------------------------------------------------------------
# 2. Version
# ---------------------------------------------------------------------------

Write-Step 'Lecture de la version'

[xml] $manifest = Get-Content -LiteralPath $manifestPath -Raw

# ATTENTION : ne PAS ecrire $manifest.extension.version.
# <extension> porte un attribut version="5.0" (version du format de
# manifeste Joomla) ET un enfant <version> (version du composant).
# L'adaptateur XML de PowerShell renverrait les deux dans un tableau.
$versionNodes = @($manifest.SelectNodes('/extension/version'))

if ($versionNodes.Count -eq 0) {
    throw "Aucun element <version> trouve dans $manifestPath."
}

if ($versionNodes.Count -gt 1) {
    throw "Le manifeste contient $($versionNodes.Count) elements <version>. Un seul est attendu."
}

$manifestVersion = ([string] $versionNodes[0].InnerText).Trim()
$manifestFormat  = $manifest.DocumentElement.GetAttribute('version')

Write-Host "    Format de manifeste Joomla : $manifestFormat"
Write-Host "    Version du composant       : [$manifestVersion]"

if ($manifestVersion -match '\s') {
    throw "La version lue vaut '$manifestVersion' : elle contient un espace."
}

if (-not $Version) {
    $Version = $manifestVersion
}

$Version = ([string] $Version).Trim()

if ($Version -ne $manifestVersion) {
    Write-Warning "La version demandee [$Version] differe de celle du manifeste [$manifestVersion]."
    Write-Warning 'Joomla se fie au manifeste : une incoherence bloque la mise a jour.'
    $answer = Read-Host 'Continuer malgre tout ? (o/N)'
    if ($answer -notmatch '^[oOyY]') { throw 'Interrompu.' }
}

if ($Version -notmatch '^\d+\.\d+\.\d+$') {
    throw "Version '$Version' invalide. Format attendu : X.Y.Z"
}

$tag     = "v$Version"
$zipName = "com_jmm-$Version.zip"

Write-Host "    Tag     : $tag"
Write-Host "    Archive : $zipName"

# ---------------------------------------------------------------------------
# 3. Construction de l'archive
#
# Toutes les operations de cette section portent -WhatIf:$false. Elles se
# deroulent dans un dossier temporaire et dans dist/, hors du depot : les
# executer meme en simulation permet de verifier reellement le paquet et
# d'obtenir son empreinte.
# ---------------------------------------------------------------------------

Write-Step 'Construction du paquet d''installation'

Add-Type -AssemblyName System.IO.Compression.FileSystem

$stagingRoot = Join-Path ([System.IO.Path]::GetTempPath()) ('jmm-build-' + [guid]::NewGuid().ToString('N'))
$staging     = Join-Path $stagingRoot 'com_jmm'
$null        = New-Item -ItemType Directory -Path $staging -Force -WhatIf:$false

$outputDir = Join-Path $RepoPath 'dist'
$null      = New-Item -ItemType Directory -Path $outputDir -Force -WhatIf:$false
$zipPath   = Join-Path $outputDir $zipName

try {
    $copied = 0

    Get-ChildItem -LiteralPath $SourcePath -Force | Where-Object {
        $ExcludeFromPackage -notcontains $_.Name
    } | ForEach-Object {
        Copy-Item -LiteralPath $_.FullName -Destination $staging -Recurse -Force -WhatIf:$false
        $copied++
    }

    if ($copied -eq 0) {
        throw "Aucun element copie depuis $SourcePath."
    }

    if (-not (Test-Path -LiteralPath (Join-Path $staging 'jmm.xml'))) {
        throw 'jmm.xml absent du dossier de preparation. Archive non conforme.'
    }

    if (Test-Path -LiteralPath $zipPath) {
        Remove-Item -LiteralPath $zipPath -Force -WhatIf:$false
    }

    [System.IO.Compression.ZipFile]::CreateFromDirectory(
        $staging,
        $zipPath,
        [System.IO.Compression.CompressionLevel]::Optimal,
        $false   # pas de dossier racine dans l'archive
    )
}
finally {
    if (Test-Path -LiteralPath $stagingRoot) {
        Remove-Item -LiteralPath $stagingRoot -Recurse -Force -WhatIf:$false -ErrorAction SilentlyContinue
    }
}

$sizeKb = [math]::Round((Get-Item -LiteralPath $zipPath).Length / 1KB, 1)
Write-Host "    $copied element(s) empaquete(s)"
Write-Host "    $zipPath ($sizeKb Ko)"

# ---------------------------------------------------------------------------
# 4. Empreinte SHA-256
# ---------------------------------------------------------------------------

Write-Step 'Calcul de l''empreinte SHA-256'

# Calcul direct via .NET plutot que Get-FileHash : cette derniere resout
# ses chemins avec "Resolve-Path | ForEach-Object ProviderPath", et
# ForEach-Object -MemberName declare ShouldProcess. Sous -WhatIf,
# l'extraction est sautee, la fonction ne recoit aucun chemin et renvoie
# un resultat vide. L'API .NET n'a pas ce probleme.
$sha256 = Get-Sha256 -Path $zipPath

Write-Host "    $sha256\"

# ---------------------------------------------------------------------------
# 5. Fichier de mise a jour
# ---------------------------------------------------------------------------

Write-Step 'Generation du fichier de mise a jour'

$downloadUrl = "https://github.com/$RepoSlug/releases/download/$tag/$zipName"

$updateXml = @"
<?xml version="1.0" encoding="utf-8"?>
<updates>
    <update>
        <name>Joomla MySQL Manager</name>
        <description>Joomla MySQL Manager (JMM) - Modern Database Management Extension for Joomla 5 and 6</description>
        <element>com_jmm</element>
        <type>component</type>
        <version>$Version</version>
        <downloads>
            <downloadurl type="full" format="zip">$downloadUrl</downloadurl>
        </downloads>
        <sha256>$sha256</sha256>
        <maintainer>Saywhat49</maintainer>
        <maintainerurl>https://github.com/$RepoSlug</maintainerurl>
        <section>Updates</section>
        <targetplatform name="joomla" version="5.*|6.*" />
        <php_minimum>8.1.0</php_minimum>
        <client>administrator</client>
    </update>
</updates>
"@

$utf8NoBom = New-Object System.Text.UTF8Encoding($false)

if ($WhatIfPreference) {
    # En simulation, on ecrit un apercu dans dist/ plutot que dans le depot.
    $previewFile = Join-Path $outputDir 'jmm_update.preview.xml'
    [System.IO.File]::WriteAllText($previewFile, $updateXml.Trim() + "`n", $utf8NoBom)
    Write-Host "    Apercu : $previewFile"
}
else {
    $updateDir  = Join-Path $RepoPath 'update'
    $null       = New-Item -ItemType Directory -Path $updateDir -Force
    $updateFile = Join-Path $updateDir 'jmm_update.xml'

    [System.IO.File]::WriteAllText($updateFile, $updateXml.Trim() + "`n", $utf8NoBom)
    $null = [xml](Get-Content -LiteralPath $updateFile -Raw)

    Write-Host "    $updateFile"
}

# ---------------------------------------------------------------------------
# 6. Synchronisation du code dans le depot
# ---------------------------------------------------------------------------

if ($inPlace) {
    Write-Step 'Synchronisation du code : inutile'
    Write-Host '    Les sources sont deja dans le depot.'
}
else {
    Write-Step 'Synchronisation du code dans le depot'

    $keepInRepo = @('.git', '.github', '.gitignore', '.vscode', 'update', 'dist')

    if ($PSCmdlet.ShouldProcess($RepoPath, 'Remplacer le code du composant')) {
        Get-ChildItem -LiteralPath $RepoPath -Force |
            Where-Object { $keepInRepo -notcontains $_.Name } |
            ForEach-Object { Remove-Item -LiteralPath $_.FullName -Recurse -Force }

        Get-ChildItem -LiteralPath $SourcePath -Force |
            Where-Object { @('.git', '.github', '.vscode', 'update', 'dist') -notcontains $_.Name } |
            ForEach-Object { Copy-Item -LiteralPath $_.FullName -Destination $RepoPath -Recurse -Force }
    }
}

# dist/ contient les archives construites : hors du depot.
$gitignore = Join-Path $RepoPath '.gitignore'
$ignoreLines = @()
if (Test-Path -LiteralPath $gitignore) {
    $ignoreLines = @(Get-Content -LiteralPath $gitignore)
}
if ($ignoreLines -notcontains 'dist/') {
    Add-Content -LiteralPath $gitignore -Value 'dist/'
}

# ---------------------------------------------------------------------------
# 7. Commit, push, release
# ---------------------------------------------------------------------------

Push-Location $RepoPath
try {
    Write-Step "Commit et push sur $Branch"

    $current = (Invoke-Native git @('rev-parse', '--abbrev-ref', 'HEAD')).Output[0].Trim()
    Write-Host "    Branche courante : $current"

    if ($current -ne $Branch) {
        if ($PSCmdlet.ShouldProcess($Branch, 'Basculer de branche')) {
            $null = Invoke-Native git @('checkout', $Branch)
        }
    }

    if ($PSCmdlet.ShouldProcess($Branch, 'Commiter et pousser')) {
        $null = Invoke-Native git @('add', '-A')

        $pending = (Invoke-Native git @('status', '--porcelain')).Output

        if ($pending.Count -eq 0) {
            Write-Host '    Aucun changement a commiter.'
        }
        else {
            Write-Host "    $($pending.Count) fichier(s) modifie(s)"
            $null = Invoke-Native git @('commit', '-m', "Release $tag")
            $null = Invoke-Native git @('push', 'origin', $Branch)
            Write-Host '    Pousse.'
        }
    }

    if (-not $SkipRelease -and $PSCmdlet.ShouldProcess($tag, 'Creer la release GitHub')) {
        Write-Step "Publication de la release $tag"

        # -AllowFailure : "release not found" est une reponse valide ici,
        # pas une panne.
        $view = Invoke-Native gh @('release', 'view', $tag, '--repo', $RepoSlug) -AllowFailure

        if ($view.ExitCode -eq 0) {
            Write-Warning "La release $tag existe deja."
            $answer = Read-Host 'Remplacer l''archive qui y est attachee ? (o/N)'

            if ($answer -match '^[oOyY]') {
                $null = Invoke-Native gh @('release', 'upload', $tag, $zipPath, '--repo', $RepoSlug, '--clobber')
                Write-Host '    Archive remplacee.'
            }
            else {
                Write-Host '    Release inchangee.'
            }
        }
        else {
            $notes = "Joomla MySQL Manager $Version" + [Environment]::NewLine + [Environment]::NewLine + "SHA-256 : ``$sha256``"

            $null = Invoke-Native gh @(
                'release', 'create', $tag, $zipPath,
                '--repo', $RepoSlug,
                '--title', "JMM $Version",
                '--notes', $notes,
                '--target', $Branch
            )
            Write-Host '    Release creee.'
        }
    }
}
finally {
    Pop-Location
}

# ---------------------------------------------------------------------------

Write-Step 'Termine'
Write-Host "    Archive        : $zipPath"
Write-Host "    SHA-256        : $sha256"
Write-Host "    Telechargement : $downloadUrl"
Write-Host "    Mise a jour    : https://raw.githubusercontent.com/$RepoSlug/$Branch/update/jmm_update.xml"

if ($WhatIfPreference) {
    Write-Host ''
    Write-Host 'Simulation : rien n''a ete ecrit dans le depot ni publie.' -ForegroundColor Yellow
}
else {
    Write-Host ''
    Write-Host 'Dans Joomla : Systeme > Mettre a jour les sites > Purger le cache,' -ForegroundColor Yellow
    Write-Host 'sinon la nouvelle version peut mettre plusieurs heures a apparaitre.' -ForegroundColor Yellow
}

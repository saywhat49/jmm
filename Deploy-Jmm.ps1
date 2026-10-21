<#
.SYNOPSIS
    Prepare et declenche une release JMM : version, commit, tag, push.

.DESCRIPTION
    Ce script ne construit PAS le paquet et ne calcule PAS le SHA-256. C'est le
    workflow GitHub (.github/workflows/release.yml), declenche par le tag, qui
    construit le zip, calcule son SHA-256, publie la release et ecrit le hash
    dans update/jmm_update.xml. Ainsi le hash est toujours celui de l'archive
    reellement telechargee par Joomla.

    Sequence :
      1. verifie que l'arbre Git est propre, sur la bonne branche, a jour ;
      2. verifie que CHANGELOG.md contient une section pour la version ;
      3. met la version a jour dans jmm.xml, media/joomla.asset.json, README.md ;
      4. lance les tests PHP si php est disponible ;
      5. commit "Release vX.Y.Z", tag annote vX.Y.Z, push de la branche et du tag.

    -WhatIf n'ecrit rien et ne pousse rien : il affiche ce qui serait fait.

.PARAMETER Version
    Version a publier, au format X.Y.Z (obligatoire).

.PARAMETER Branch
    Branche de publication. Par defaut master, comme l'URL du serveur de
    mises a jour declaree dans jmm.xml.

.EXAMPLE
    .\Deploy-Jmm.ps1 -Version 5.7.0 -WhatIf

.EXAMPLE
    .\Deploy-Jmm.ps1 -Version 5.7.0
#>

#Requires -Version 5.1

[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [Parameter(Mandatory = $true)]
    [ValidatePattern('^\d+\.\d+\.\d+$')]
    [string] $Version,

    [string] $Branch = 'master'
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$Root = $PSScriptRoot
$Tag  = "v$Version"
$Utf8 = New-Object System.Text.UTF8Encoding($false)

function Write-Step {
    param([string] $Message)
    Write-Host ''
    Write-Host "==> $Message" -ForegroundColor Cyan
}

function Invoke-Git {
    # Fonction simple ($args) : les options comme -m ou --porcelain sont
    # transmises telles quelles a git. Git ecrit sa progression sur stderr ;
    # avec ErrorActionPreference = Stop, Windows PowerShell 5.1 y verrait une
    # erreur. On ne juge donc que le code de sortie.
    $ErrorActionPreference = 'Continue'
    $output = & git -C $Root @args 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "git $($args -join ' ') a echoue : $($output | Out-String)"
    }
    return ($output | Out-String).Trim()
}

function Set-FileText {
    # Reecrit un fichier en UTF-8 sans BOM, en conservant ses fins de ligne.
    param([string] $Path, [string] $Pattern, [string] $Replacement)
    $text = [System.IO.File]::ReadAllText($Path, $Utf8)
    if ($text -notmatch $Pattern) {
        throw "Motif introuvable dans $Path : $Pattern"
    }
    $updated = [regex]::Replace($text, $Pattern, $Replacement, 'None')
    if ($PSCmdlet.ShouldProcess($Path, "Mettre a jour vers $Version")) {
        [System.IO.File]::WriteAllText($Path, $updated, $Utf8)
    }
}

# ------------------------------------------------------------------ 1. Git
Write-Step 'Controle du depot'

if (-not (Get-Command git -ErrorAction SilentlyContinue)) { throw "git est introuvable dans le PATH." }

$current = Invoke-Git rev-parse --abbrev-ref HEAD
if ($current -ne $Branch) { throw "Vous etes sur '$current' : placez-vous sur '$Branch' avant de publier." }

if (Invoke-Git status --porcelain) { throw "L'arbre de travail n'est pas propre. Committez ou remisez vos changements." }

Invoke-Git fetch origin $Branch --tags | Out-Null
if ((Invoke-Git rev-parse HEAD) -ne (Invoke-Git rev-parse "origin/$Branch")) {
    throw "'$Branch' n'est pas a jour avec origin/$Branch (pull ou push d'abord)."
}

if (Invoke-Git tag --list $Tag) { throw "Le tag $Tag existe deja." }

# ------------------------------------------------------- 2. Journal des changements
Write-Step 'Controle du journal des changements'

$changelog = Join-Path $Root 'CHANGELOG.md'
if (-not (Test-Path -LiteralPath $changelog)) { throw 'CHANGELOG.md est introuvable.' }

$escaped = [regex]::Escape($Version)
if ([System.IO.File]::ReadAllText($changelog, $Utf8) -notmatch "(?m)^## \[?$escaped\]?") {
    throw "CHANGELOG.md n'a pas de section '## [$Version]'. Ecrivez les notes de version d'abord : elles deviennent le texte de la release."
}
Write-Host "    Section $Version presente."

# ------------------------------------------------------- 3. Numeros de version
Write-Step "Version $Version"

$today = Get-Date -Format 'yyyy-MM-dd'
Set-FileText (Join-Path $Root 'jmm.xml') '(?<=<version>)[^<]*(?=</version>)' $Version
Set-FileText (Join-Path $Root 'jmm.xml') '(?<=<creationDate>)[^<]*(?=</creationDate>)' $today
Set-FileText (Join-Path $Root 'media/joomla.asset.json') '(?<="version":\s*")[^"]*(?=")' $Version
Set-FileText (Join-Path $Root 'README.md') '(?<=\(v)\d+\.\d+\.\d+(?=\))' $Version

# ------------------------------------------------------- 4. Tests
Write-Step 'Tests'

if (Get-Command php -ErrorAction SilentlyContinue) {
    & php (Join-Path $Root 'tests/run.php')
    if ($LASTEXITCODE -ne 0) { throw 'Les tests ont echoue : publication annulee.' }
} else {
    Write-Warning "php est introuvable : tests ignores ici (la CI les rejouera avant la release)."
}

# ------------------------------------------------------- 5. Commit, tag, push
Write-Step "Commit, tag $Tag et push"

if ($PSCmdlet.ShouldProcess("$Branch / $Tag", 'Committer, tagger et pousser')) {
    Invoke-Git add jmm.xml media/joomla.asset.json README.md | Out-Null

    # Rien a committer si la version etait deja la bonne : le tag suffit alors.
    if (Invoke-Git status --porcelain) {
        Invoke-Git commit -m "Release $Tag" | Out-Null
    }

    Invoke-Git tag -a $Tag -m "JMM $Version" | Out-Null
    Invoke-Git push origin $Branch | Out-Null
    Invoke-Git push origin $Tag | Out-Null

    Write-Host ''
    Write-Host "Tag $Tag pousse. Le workflow 'Release' construit le paquet, calcule le SHA-256," -ForegroundColor Green
    Write-Host 'cree la release et met a jour update/jmm_update.xml.' -ForegroundColor Green
    Write-Host "Suivi : https://github.com/saywhat49/jmm/actions"
    Write-Host 'Ensuite : Systeme > Mises a jour > Sites de mise a jour, videz le cache (Purger le cache).'
} else {
    Write-Host "Simulation terminee : rien n'a ete ecrit ni pousse."
}

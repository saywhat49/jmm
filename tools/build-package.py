#!/usr/bin/env python3
"""Construit le paquet Joomla de JMM et prepare tout ce qui en depend.

Une seule source de verite pour la publication : ce script est appele par le
workflow GitHub (tag vX.Y.Z) et peut l'etre a la main pour une repetition.

Il fait, dans cet ordre :
  1. lit la version dans jmm.xml et la confronte au tag et a joomla.asset.json ;
  2. construit dist/com_jmm-<version>.zip ;
  3. calcule le SHA-256 DE CE FICHIER PRECIS ;
  4. ecrit dist/com_jmm-<version>.zip.sha256 (lisible par `sha256sum -c`) ;
  5. genere le fichier de mise a jour du serveur de mises a jour Joomla, avec
     la version, l'URL de telechargement et le SHA-256 du zip ci-dessus ;
  6. ecrit les notes de version depuis CHANGELOG.md.

L'ordre compte : le hash doit etre celui de l'archive reellement publiee.
Recompresser les memes fichiers peut produire des octets differents, donc un
hash different, et Joomla refuserait la mise a jour (somme de controle
invalide). C'est pourquoi le zip est construit de facon reproductible
(entrees triees, horodatage fixe) et que le hash est calcule apres coup,
sur le fichier envoye a GitHub.

Bibliotheque standard uniquement.
"""

import argparse
import hashlib
import json
import re
import sys
import zipfile
from pathlib import Path
from xml.dom import minidom

# Ce qui vit dans le depot mais ne doit PAS etre installe par Joomla.
EXCLUDE_TOP = {
    '.git', '.github', '.gitignore', '.gitattributes', '.vscode', '.editorconfig',
    'update', 'dist', 'build', 'tests', 'tools',
    'README.md', 'CHANGELOG.md',
}
EXCLUDE_NAMES = {'.DS_Store', 'Thumbs.db'}
EXCLUDE_SUFFIXES = ('.zip', '.sha256')

# Horodatage fixe des entrees du zip (le minimum que le format permette).
ZIP_DATE = (1980, 1, 1, 0, 0, 0)

VERSION_RE = re.compile(r'^\d+\.\d+\.\d+$')


def fail(message):
    print('ERREUR : ' + message, file=sys.stderr)
    sys.exit(1)


def read_version(root):
    manifest = root / 'jmm.xml'
    if not manifest.is_file():
        fail('jmm.xml introuvable dans ' + str(root))

    dom = minidom.parse(str(manifest))
    nodes = dom.documentElement.getElementsByTagName('version')
    if not nodes or not nodes[0].firstChild:
        fail('balise <version> absente de jmm.xml')

    version = nodes[0].firstChild.data.strip()
    if not VERSION_RE.match(version):
        fail('version invalide dans jmm.xml : ' + repr(version))
    return version


def check_consistency(root, version, tag):
    if tag is not None:
        expected = tag[1:] if tag.startswith('v') else tag
        if expected != version:
            fail('le tag %s ne correspond pas a la version %s de jmm.xml. '
                 'Corrigez jmm.xml (et media/joomla.asset.json) avant de tagger.' % (tag, version))

    assets = root / 'media' / 'joomla.asset.json'
    if assets.is_file():
        declared = json.loads(assets.read_text(encoding='utf-8')).get('version')
        if declared != version:
            fail('media/joomla.asset.json declare la version %s, jmm.xml la version %s.' % (declared, version))


def package_files(root):
    files = []
    for path in sorted(root.rglob('*'), key=lambda p: p.relative_to(root).as_posix()):
        if not path.is_file():
            continue
        rel = path.relative_to(root)
        if rel.parts[0] in EXCLUDE_TOP:
            continue
        if path.name in EXCLUDE_NAMES or path.name.endswith(EXCLUDE_SUFFIXES):
            continue
        files.append((rel.as_posix(), path))
    return files


def build_zip(root, out_path):
    files = package_files(root)
    if not any(name == 'jmm.xml' for name, _ in files):
        fail('jmm.xml absent de la liste des fichiers du paquet')

    out_path.parent.mkdir(parents=True, exist_ok=True)
    if out_path.exists():
        out_path.unlink()

    with zipfile.ZipFile(out_path, 'w', zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for name, path in files:
            info = zipfile.ZipInfo(name, date_time=ZIP_DATE)
            info.compress_type = zipfile.ZIP_DEFLATED
            info.external_attr = 0o644 << 16
            info.create_system = 3  # Unix : memes octets quel que soit le systeme de build
            archive.writestr(info, path.read_bytes(), compresslevel=9)

    return len(files)


def sha256_of(path):
    digest = hashlib.sha256()
    with path.open('rb') as handle:
        for chunk in iter(lambda: handle.read(1024 * 1024), b''):
            digest.update(chunk)
    return digest.hexdigest()


def write_update_xml(source, target, version, url, sha256):
    """Reprend update/jmm_update.xml et n'y change que version, URL et hash."""
    text = source.read_text(encoding='utf-8')

    def swap(pattern, replacement, label):
        nonlocal text
        text, count = re.subn(pattern, replacement, text, count=1, flags=re.S)
        if count != 1:
            fail('balise %s introuvable dans %s' % (label, source))

    swap(r'(<update>.*?<version>)[^<]*(</version>)', r'\g<1>' + version + r'\g<2>', '<version>')
    swap(r'(<downloadurl[^>]*>)[^<]*(</downloadurl>)', r'\g<1>' + url + r'\g<2>', '<downloadurl>')
    swap(r'(<sha256>)[^<]*(</sha256>)', r'\g<1>' + sha256 + r'\g<2>', '<sha256>')

    minidom.parseString(text.encode('utf-8'))  # le XML produit doit etre valide

    target.parent.mkdir(parents=True, exist_ok=True)
    target.write_text(text, encoding='utf-8', newline='')


def release_notes(root, version, sha256, zip_name):
    notes = ''
    changelog = root / 'CHANGELOG.md'

    if changelog.is_file():
        text = changelog.read_text(encoding='utf-8')
        match = re.search(r'^## \[?' + re.escape(version) + r'\]?.*?$\n(.*?)(?=^## |\Z)', text, flags=re.S | re.M)
        if match:
            notes = match.group(1).strip()

    if not notes:
        notes = 'Release automatique de JMM %s, compatible Joomla 5 et 6 (PHP 8.1 a 8.3).' % version

    return '%s\n\n---\n\n**SHA-256** de `%s` :\n\n```\n%s\n```\n' % (notes, zip_name, sha256)


def main():
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument('--root', default='.', help='racine du depot (jmm.xml a la racine)')
    parser.add_argument('--out', default='dist', help='dossier de sortie')
    parser.add_argument('--tag', help='tag Git vX.Y.Z : doit correspondre a la version de jmm.xml')
    parser.add_argument('--repo', default='saywhat49/jmm', help='depot GitHub (proprietaire/nom)')
    parser.add_argument('--update-xml-in', default='update/jmm_update.xml', help='modele du fichier de mise a jour')
    parser.add_argument('--update-xml-out', help='ou ecrire le fichier de mise a jour genere (rien par defaut)')
    args = parser.parse_args()

    root = Path(args.root).resolve()
    out = Path(args.out).resolve()

    version = read_version(root)
    check_consistency(root, version, args.tag)

    zip_name = 'com_jmm-%s.zip' % version
    zip_path = out / zip_name
    count = build_zip(root, zip_path)

    sha256 = sha256_of(zip_path)
    (out / (zip_name + '.sha256')).write_text('%s  %s\n' % (sha256, zip_name), encoding='utf-8', newline='')

    tag = args.tag or ('v' + version)
    url = 'https://github.com/%s/releases/download/%s/%s' % (args.repo, tag, zip_name)

    if args.update_xml_out:
        write_update_xml(root / args.update_xml_in, Path(args.update_xml_out).resolve(), version, url, sha256)

    (out / 'release-notes.md').write_text(release_notes(root, version, sha256, zip_name), encoding='utf-8', newline='')

    print('Version        : ' + version)
    print('Paquet         : %s (%d fichiers, %d octets)' % (zip_path, count, zip_path.stat().st_size))
    print('SHA-256        : ' + sha256)
    print('Telechargement : ' + url)
    if args.update_xml_out:
        print('Mise a jour    : ' + str(Path(args.update_xml_out).resolve()))


if __name__ == '__main__':
    main()

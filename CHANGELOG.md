# Changelog

Format : [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/). Chaque section `## [X.Y.Z]` devient le texte de la release GitHub du même numéro.

## [5.7.0] - 2026-10-22

### Sécurité

- **Requête SQL : le jeton CSRF est maintenant vérifié.** L'exécution d'une requête depuis l'écran « Exécuter SQL » ne contrôlait pas le jeton : un site tiers pouvait soumettre une requête au nom d'un administrateur connecté.
- **Écriture SQL réservée à `core.admin`.** Le droit `core.manage` (Manager) ne permet plus que la lecture (`SELECT`, `WITH` sans écriture, `SHOW`, `DESCRIBE`, `EXPLAIN`). `INSERT`, `UPDATE`, `DELETE` et le DDL demandent `core.admin`.
- **L'export CSV ne lit plus que.** Il exécutait n'importe quelle instruction.
- **Les tables de site ne peuvent plus écrire.** L'affichage public exécutait la requête enregistrée sans contrôler son type ; seul l'appel AJAX le faisait.
- **Plus de repli silencieux sur la base de Joomla.** Si la connexion dédiée (ou vers une autre base) échoue, une erreur explicite est affichée aux gestionnaires au lieu d'exécuter les requêtes sur la mauvaise base. Le détail du pilote (hôte, compte MySQL) n'est jamais montré aux visiteurs.
- **Noms de bases, de tables et de colonnes refusés, plus « nettoyés ».** Retirer des caractères en silence pouvait viser une autre table. Les noms sont aussi vérifiés contre `SHOW TABLES` avant usage.
- **XSS :** le gabarit de départ échappe les valeurs ; les modèles fournis `default`, `blog`, `blog4`, `camembert`, `camenbert-totaux` et `piechart` échappent le HTML et encodent en JSON les données insérées dans du JavaScript ; le type de graphique est validé par liste blanche.
- **Plugins de contenu : désactivés par défaut** sur la sortie des modèles. Une valeur de la base pouvait déclencher un code court Joomla. Nouvelle option de menu « Appliquer les plugins de contenu à la sortie ».

### Corrigé

- Le formulaire « Insérer des données » provoquait une erreur fatale (mauvais espace de noms de `TablefieldsField`).
- Une requête de table de site qui contenait déjà `LIMIT` ou `ORDER BY`, ou qui finissait par un commentaire, provoquait une erreur ; `SHOW` et `DESCRIBE` échouaient au comptage. La requête est désormais enveloppée dans une sous-requête (`SHOW`/`DESCRIBE`/`EXPLAIN` sont paginés côté PHP).
- Une page demandée au-delà de la dernière revient à la dernière page.
- « Insérer des données » ne peut plus envoyer `''` dans une colonne numérique ou une date : `NULL` si la colonne l'accepte, sinon la valeur par défaut.

### Modifié

- La requête d'une table de site n'est plus exécutée deux fois par page ; le tri AJAX ne lance plus de requête d'essai.
- Les messages de diagnostic du site et de l'installation passent par les fichiers de langue (en-GB, fr-FR).
- `script.php` : suppression du code mort (`migrateTemplatesTable`), des appels redondants et de l'indentation cassée.
- Publication : **un seul chemin**. Le tag `vX.Y.Z` déclenche le workflow qui construit le paquet, calcule son SHA-256, crée la release et écrit le hash dans `update/jmm_update.xml`. Le script `Deploy-Jmm.ps1` est supprimé : la version se prépare à la main (voir README).
- CI : syntaxe PHP 8.1/8.2/8.3, tests, XML, cohérence des fichiers de langue, essai de construction du paquet.
- Ajout de `LICENSE.txt` (GPL v2), `.editorconfig`, tests de `QueryHelper` (`php tests/run.php`).

### À savoir en mettant à jour

- Un utilisateur qui exécutait des requêtes d'écriture avec le seul droit `core.manage` obtient désormais un refus : accordez-lui `core.admin` sur le composant ou faites exécuter ces requêtes par un super utilisateur.
- Si vos modèles personnalisés comptaient sur les plugins de contenu, activez l'option du même nom dans l'élément de menu.
- **Les modèles déjà installés ne sont pas écrasés** (c'est voulu : vos modifications sont conservées). Les corrections apportées aux modèles fournis n'atteignent donc que les nouvelles installations. Pour en profiter sur un site existant, sauvegardez puis supprimez le dossier du modèle concerné dans `/components/com_jmm/templates/` avant d'installer 5.7.0, ou reportez à la main l'échappement des valeurs.
- Après la publication d'une release, videz le cache des sites de mise à jour (Système > Mises à jour > Sites de mise à jour).

## [5.6.4] - 2026-08-31

Dernière version avant la refonte de la sécurité et de la publication.

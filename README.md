# JMM - Joomla MySQL Manager (v5.7.0)

**JMM (Joomla MySQL Manager)** is a database management component for **Joomla 5** and **Joomla 6**, written for **PHP 8.1 and later** (tested on 8.1, 8.2 and 8.3).

---

## 🚀 Key Features

* **Database Explorer**: View and inspect all databases accessible by your database user.
* **Table Inspector**: Browse tables, table structures, indexes, and storage engine metrics.
* **SQL Query Runner**: Execute custom SQL queries with execution time metrics and direct CSV export.
* **Canned Queries**: Save frequent queries for fast re-execution with a single click.
* **Site Tables**: Publish read-only database queries to frontend menu views with pagination.
* **Custom PHP Templates**: Each template owns a folder under `/components/com_jmm/templates/<name>/` holding `index.php`, `css/default.css` and `js/custom.js`, all editable from the administrator. Built-in table, card and chart layouts remain available.
* **Table Designer**: Create new database tables visually with columns, types, indexes, and primary keys.
* **Data Inserter**: Insert rows easily with column-specific field editors.
* **CSV Export**: Stream and download the results of a read-only query, with formula injection protection.

---

## 🛡️ Security & Architecture

* **Joomla 5 & 6 Native Architecture**: Strict PSR-4 autoloading (`Saywhat49\Component\Jmm`), Dependency Injection container (`services/provider.php`), no legacy B/C dependency.
* **Read or write, by permission**: Users with `core.manage` can run read-only statements (`SELECT`, `WITH` without writes, `SHOW`, `DESCRIBE`, `EXPLAIN`). Anything that modifies data or the schema (`INSERT`, `UPDATE`, `DELETE`, `DROP`, `ALTER`...) requires `core.admin`. The classifier lives in `QueryHelper` and is covered by tests. It is a consistency guard, not a substitute for MySQL privileges: give the database user only the rights it needs.
* **Site tables never write**: The query behind a published site table is refused unless it is read-only, both for the page and for the AJAX endpoint.
* **No silent fallback**: Each target database gets its own cached connection. The shared Joomla `DatabaseDriver` is never switched with `USE`. If a dedicated connection fails, an error is reported to component managers (never the driver message to visitors); queries are not run against the Joomla database instead.
* **Strict identifiers**: Database, table and column names are accepted as typed or refused, never "cleaned" into another name. Table names are checked against `SHOW TABLES` before use.
* **PHP Template Editing Restricted**: Writing `index.php` and `js/custom.js` from the administrator requires `core.admin` on `com_jmm`. Users limited to `core.edit` can still edit the stylesheet.
* **Non-Destructive Template Deployment**: Bundled templates are copied by the install script only when the destination folder is absent, so edits made through the administrator survive updates.
* **Access Control List (ACL)**: Granular permissions (`core.manage`, `core.admin`, `core.create`, `core.edit`, `core.delete`).
* **CSRF Protection**: Session token checks on all forms, including SQL execution, and on Fetch API endpoints.
* **XSS Mitigation**: Output escaping across backend and frontend templates; the bundled templates escape values and JSON-encode data placed in JavaScript.
* **CSV Injection Prevention (CWE-1236)**: Spreadsheet formulas are automatically escaped on export.
* **Modern UI**: Bootstrap 5 integration matching the Joomla Atum template, Vanilla ES6 JavaScript.

---

## 🎨 Writing a Custom Template

A template folder is laid out as follows:

```
components/com_jmm/templates/<name>/
├── index.php          # rendering code
├── css/default.css    # loaded automatically on the frontend
├── js/custom.js       # loaded automatically, deferred
└── images/
```

Inside `index.php` the following are available:

| Variable | Contents |
| --- | --- |
| `$rows` | records, as associative arrays |
| `$cols` | column names |
| `$params` | menu item parameters |
| `$document` | the Joomla document |
| `$this->pagination` | the pagination object |
| `$this->defaultPagination` | set to `false` to suppress the pagination block |

Set the template's layout to **Custom PHP template**, then select it in the menu item under **JMM template**. Errors raised by a template are caught and reported to users holding `core.manage`, instead of breaking the page.

> **Always escape what you print.** Values come from the database and appear on the public site. Use `$this->escape($row['title'])`. Print a value raw only when you are sure it cannot hold HTML typed by a third party; for an HTML body, filter it first (for example with `\Joomla\CMS\Filter\InputFilter`).
> Content plugins (shortcodes, email cloaking...) are **not** applied to the output by default. Turn on **Run content plugins on the output** in the menu item if a template needs them.

---

## 📦 Installation & Requirements

* **Joomla Version**: Joomla 5.x or Joomla 6.x
* **PHP Version**: PHP 8.1 or later (8.1, 8.2 and 8.3 are tested)
* **Database**: MySQL 8.x / MariaDB 10.4+

1. Download the latest release from the [Releases](https://github.com/saywhat49/jmm/releases) page.
2. In your Joomla Administrator, go to **System > Install > Extensions**.
3. Upload and install the component package (`com_jmm-X.Y.Z.zip`).
4. Access the component via **Components > Joomla MySQL Manager**.

Each release ships a `com_jmm-X.Y.Z.zip.sha256` file next to the zip. To check a download: `sha256sum -c com_jmm-X.Y.Z.zip.sha256`.

Updates are served from `update/jmm_update.xml` in this repository, declared as an update server in `jmm.xml`. After a release, purge the update cache under **System > Update Sites**.

---

## 🔧 Development & Releases

Run the tests with `php tests/run.php`. The CI (`.github/workflows/ci.yml`) also lints PHP 8.1 to 8.3, validates the XML and language files, and does a dry-run build of the package.

**Publishing a release** has a single path, so that the SHA-256 in the update file is always the hash of the archive Joomla downloads:

1. Add a `## [X.Y.Z]` section to `CHANGELOG.md` (it becomes the release notes).
2. Set the same version in `jmm.xml`, `media/joomla.asset.json` and the title of this file, run `php tests/run.php`, then commit and push to `master`.
3. Tag the commit and push the tag: `git tag vX.Y.Z && git push origin vX.Y.Z`.
4. The tag triggers `.github/workflows/release.yml`, which builds the package with `tools/build-package.py`, computes its SHA-256, creates the GitHub release and commits the new version, download URL and hash to `update/jmm_update.xml` on `master`.

Do not edit `update/jmm_update.xml` by hand and do not attach a zip built elsewhere: a rebuilt archive has different bytes, hence a different hash, and Joomla would reject the update.

---

## 📄 License

GNU General Public License version 2 or later; see [LICENSE.txt](LICENSE.txt).
Copyright (C) 2013-2026 Saywhat49 and contributors. Original JMM: Biswarup Adhikari.

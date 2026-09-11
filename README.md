# JMM - Joomla MySQL Manager (v5.4.2)

**JMM (Joomla MySQL Manager)** is a database management component for **Joomla 5** and **Joomla 6** written in modern **PHP 8.2 / 8.3**.

---

## 🚀 Key Features

* **Database Explorer**: View and inspect all databases accessible by your database user.
* **Table Inspector**: Browse tables, table structures, indexes, and storage engine metrics.
* **SQL Query Runner**: Execute custom SQL queries with execution time metrics and direct CSV export.
* **Canned Queries**: Save frequent queries for fast re-execution with a single click.
* **Site Tables**: Publish customized database queries to frontend menu views with pagination.
* **Custom PHP Templates**: Each template owns a folder under `/components/com_jmm/templates/<name>/` holding `index.php`, `css/default.css` and `js/custom.js`, all editable from the administrator. Built-in table, card and chart layouts remain available.
* **Table Designer**: Create new database tables visually with columns, types, indexes, and primary keys.
* **Data Inserter**: Insert rows easily with column-specific field editors.
* **CSV Export**: Stream and download query results directly into CSV with formula injection protection.

---

## 🛡️ Security & Architecture

* **Joomla 5 & 6 Native Architecture**: Strict PSR-4 autoloading (`Saywhat49\Component\Jmm`), Dependency Injection container (`services/provider.php`), no legacy B/C dependency.
* **Per-Database Connections**: Each target database gets its own cached connection. The shared Joomla `DatabaseDriver` is never switched with `USE`, which would leak onto session writes and every other query in the request. Connection failures are reported instead of silently falling back.
* **PHP Template Editing Restricted**: Writing `index.php` and `js/custom.js` from the administrator requires `core.admin` on `com_jmm`. Users limited to `core.edit` can still edit the stylesheet.
* **Non-Destructive Template Deployment**: Bundled templates are copied by the install script only when the destination folder is absent, so edits made through the administrator survive updates.
* **SQL Injection Hardening**: All table names, column names, and inputs are properly quoted and sanitized with whitelist validation.
* **Access Control List (ACL)**: Granular permissions for Super Administrators and Managers (`core.manage`, `core.admin`, `core.create`, `core.edit`, `core.delete`).
* **CSRF Protection**: Native session token checks on all forms and Fetch API endpoints.
* **XSS Mitigation**: Context-aware output escaping across all backend and frontend templates.
* **CSV Injection Prevention (CWE-1236)**: Spreadsheets formulas are automatically escaped on export.
* **Modern UI**: Bootstrap 5 integration matching the Joomla Atum template, Vanilla ES6 JavaScript (zero jQuery `.live()` dependency).

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

---

## 📦 Installation & Requirements

* **Joomla Version**: Joomla 5.x or Joomla 6.x
* **PHP Version**: PHP 8.1, 8.2 or 8.3+
* **Database**: MySQL 8.x / MariaDB 10.4+

1. Download the latest release from the [Releases](https://github.com/saywhat49/jmm/releases) page.
2. In your Joomla Administrator, go to **System > Install > Extensions**.
3. Upload and install the component package (`com_jmm-X.Y.Z.zip`).
4. Access the component via **Components > Joomla MySQL Manager**.

Updates are served from `update/jmm_update.xml` in this repository, declared as an update server in `jmm.xml`. After publishing a release, purge the update cache under **System > Update Sites**.

---

## 📄 License

GNU General Public License version 2 or later; see [LICENSE.txt](LICENSE.txt).
Copyright (C) 2013-2026 Saywhat49. All rights reserved.

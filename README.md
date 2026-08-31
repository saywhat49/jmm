# JMM - Joomla MySQL Manager (v5.2.0)

**JMM (Joomla MySQL Manager)** is a database management component for **Joomla 5** and **Joomla 6** written in modern **PHP 8.2 / 8.3**.

---

## 🚀 Key Features

* **Database Explorer**: View and inspect all databases accessible by your database user.
* **Table Inspector**: Browse tables, table structures, indexes, and storage engine metrics.
* **SQL Query Runner**: Execute custom SQL queries with execution time metrics and direct CSV export.
* **Canned Queries**: Save frequent queries for fast re-execution with a single click.
* **Site Tables**: Publish customized database queries to frontend menu views with pagination.
* **Table Designer**: Create new database tables visually with columns, types, indexes, and primary keys.
* **Data Inserter**: Insert rows easily with column-specific field editors.
* **CSV Export**: Stream and download query results directly into CSV with formula injection protection.

---

## 🛡️ Security & Architecture

* **Joomla 5 & 6 Native Architecture**: Strict PSR-4 autoloading (`Saywhat49\Component\Jmm`), Dependency Injection container (`services/provider.php`), no legacy B/C dependency.
* **SQL Injection Hardening**: All table names, column names, and inputs are properly quoted and sanitized with whitelist validation.
* **Access Control List (ACL)**: Granular permissions for Super Administrators and Managers (`core.manage`, `core.admin`, `core.create`, `core.edit`, `core.delete`).
* **CSRF Protection**: Native session token checks on all forms and Fetch API endpoints.
* **XSS Mitigation**: Context-aware output escaping across all backend and frontend templates.
* **CSV Injection Prevention (CWE-1236)**: Spreadsheets formulas are automatically escaped on export.
* **Modern UI**: Bootstrap 5 integration matching the Joomla Atum template, Vanilla ES6 JavaScript (zero jQuery `.live()` dependency).

---

## 📦 Installation & Requirements

* **Joomla Version**: Joomla 5.x or Joomla 6.x
* **PHP Version**: PHP 8.1, 8.2 or 8.3+
* **Database**: MySQL 8.x / MariaDB 10.4+

1. Download the latest release from the [Releases](https://github.com/saywhat49/jmm/releases) page.
2. In your Joomla Administrator, go to **System > Install > Extensions**.
3. Upload and install the component package (`com_jmm-5.2.0.zip` or `jmm-5.2.0-j5-j6.zip`).
4. Access the component via **Components > Joomla MySQL Manager**.

---

## 📄 License

GNU General Public License version 2 or later; see [LICENSE.txt](LICENSE.txt).
Copyright (C) 2013-2026 Saywhat49. All rights reserved.
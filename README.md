# Organization Document Manager

A **multi-tenant document management system** built with Laravel 11 and Filament 4. Organizations can define dynamic forms, create and manage documents, set email reminders, and communicate via private, general, and support chat.

---

## Features

- **Dynamic form builder** – Create forms with multiple versions; JSON schema drives Filament-rendered fields (text, number, date, Jalali date, select, file, etc.).
- **Document management** – Create, edit, view, and delete documents; store form data and attachments; optional status and title patterns.
- **Automated reminders** – Email reminders based on date fields in documents; optional extra recipients; processed by a scheduled Artisan command.
- **Multi-mode chat** – Private (1‑to‑1), general (organization-wide), and support (organization ↔ system administrators).
- **Reports** – App: statistics, charts (by form, month, status, creator, and form fields), CSV export. Admin: filtered document and user-activity reports.
- **Multi-tenancy** – Strict isolation by organization; role-based access per organization.
- **Localization** – English and Persian (Farsi); Jalali (Solar) date support.

---

## Documentation

All documentation is in the **[`/docs`](docs/)** directory:

| Document | Description |
|----------|-------------|
| [**docs/README.md**](docs/README.md) | Documentation index and quick links. |
| [**docs/USER.md**](docs/USER.md) | **User guide** – documents, reminders, chat, reports, settings, FAQ. |
| [**docs/ENGINEER.md**](docs/ENGINEER.md) | **Developer guide** – tech stack, architecture, guidelines, services. |
| [**docs/INSTALL.md**](docs/INSTALL.md) | **Installation** – prerequisites, environment, database, scheduler. |
| [**docs/ARCHITECTURE.md**](docs/ARCHITECTURE.md) | **Architecture** – panels, multi-tenancy, models, main flows. |

---

## Quick start

1. **Clone and install**
   ```bash
   git clone <repository-url>
   cd laravel-organization-document-manager
   composer install
   npm install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` (database, `APP_URL`, etc.). See [docs/INSTALL.md](docs/INSTALL.md).

3. **Database**
   ```bash
   php artisan migrate --seed
   ```

4. **Run**
   ```bash
   php artisan serve
   ```
   - **App panel:** [http://localhost:8000/app](http://localhost:8000/app) (organization users).
   - **Admin panel:** [http://localhost:8000/admin](http://localhost:8000/admin) (General Managers only).

5. **Reminders** – Schedule the reminder command (e.g. every minute via cron). See [docs/INSTALL.md](docs/INSTALL.md#scheduler-reminders).

---

## License

[MIT](LICENSE)

# Software engineer documentation

This document is for **developers and software engineers** working on **Organization Document Manager**. It covers technologies, architecture, development guidelines, and key implementation details.

**Audience:** Laravel/PHP developers, maintainers, contributors.

---

## Table of contents

1. [Project overview](#project-overview)
2. [Technology stack](#technology-stack)
3. [Architecture summary](#architecture-summary)
4. [Setup and development](#setup-and-development)
5. [Development guidelines](#development-guidelines)
6. [Key services and utilities](#key-services-and-utilities)
7. [Localization](#localization)
8. [Security and multi-tenancy](#security-and-multi-tenancy)
9. [Testing and deployment](#testing-and-deployment)
10. [Further reading](#further-reading)

---

## Project overview

Organization Document Manager is a **multi-tenant document management system** that provides:

- **Dynamic form builder** – Forms and versions with JSON schemas; Filament renders fields at runtime.
- **Document lifecycle** – Create, edit, view, delete documents; store form data as JSON; optional file attachments.
- **Automated reminders** – Email reminders based on date fields; processed by a scheduled Artisan command.
- **Multi-mode chat** – Private (1‑to‑1), general (org-wide), and support (org ↔ General Manager).
- **Reports** – App-side: stats and charts per organization, CSV export; Admin-side: filtered stats and user activity (audit log).
- **Role-based access** – Permissions and roles per organization; General Manager for global admin.

Data is **strictly isolated by organization** (`organization_id` on tenant-scoped models).

---

## Technology stack

| Layer | Technology |
|-------|------------|
| **Framework** | Laravel 11 |
| **Admin / App UI** | Filament 4 (Schemas, Resources, Pages, Widgets) |
| **Frontend** | Livewire 3, Tailwind CSS, Alpine.js |
| **Database** | MySQL / MariaDB or SQLite (via Laravel migrations) |
| **Date handling** | Carbon; Jalali/Persian via `morilog/jalali`, `mokhosh/filament-jalali` |
| **Calendar (Filament)** | `saade/filament-fullcalendar` |
| **Language switch** | `bezhansalleh/filament-language-switch` |

---

## Architecture summary

- **Two Filament panels:**
  - **Admin** (`/admin`) – General Managers only; manage organizations, users, forms, form versions, audit logs, roles/permissions; admin reports and support chat.
  - **App** (`/app`) – Tenant panel; organization-scoped documents, users, roles, chat, reports, reminders, settings.
- **Multi-tenancy:** Tenant scope enforced by `organization_id` and middleware (`EnsureTenantAccess`, `EnsureGeneralManager`).
- **Forms:** Stored as `Form` + `FormVersion`; schema in `form_versions.schema` (JSON). `FormSchemaService` compiles schema to Filament components; document data stored in `documents.data` (JSON).
- **Chat:** `ChatMessage` (general + support), `PrivateChat` (1‑to‑1); support handled in Admin via `SupportChat` page.
- **Reminders:** `Reminder` model; `app:process-reminders` command; emails via `DocumentReminderMail` and queue (optional).
- **Audit:** `AuditLog` for user actions; used in Admin reports (activity by action, user, organization).

For detailed architecture (models, relationships, flows), see [ARCHITECTURE.md](ARCHITECTURE.md).

---

## Setup and development

- Full installation steps: [INSTALL.md](INSTALL.md).
- After clone: `composer install`, `cp .env.example .env`, `php artisan key:generate`, configure DB, `php artisan migrate --seed`.
- Schedule: `app:process-reminders` every minute (see `routes/console.php`).
- Local server: `php artisan serve`; Admin at `/admin`, App at `/app`.

---

## Development guidelines

### Filament 4

- Use **`Filament\Schemas\Schema`** for form and infolist definitions (e.g. `Form::configure($schema)`, `Infolist::configure($schema)`).
- Use **`->schema([...])`** (not `->components([...])`) on schema objects where the API expects it.
- **Pages:** `$view` must be **non-static** when extending `Filament\Pages\Page` (e.g. `protected string $view = '...';`).
- **Chart widgets:** Extend `Filament\Widgets\ChartWidget`; implement `getData()` and `getType()`. For multiple charts on one view, use a custom widget view and Filament’s chart Alpine component with distinct data per chart.

### Queries with joins

- When joining tables that both have `organization_id` (e.g. `documents` + `users`), **qualify the column** in `where()` to avoid ambiguity: e.g. `->where('documents.organization_id', $orgId)`.

### Code style

- Follow PSR-12. The project uses **Laravel Pint** (`laravel/pint`) for formatting; run `./vendor/bin/pint` if configured.

---

## Key services and utilities

| Service / class | Purpose |
|-----------------|--------|
| **`FormSchemaService`** | Validates form schema; builds validation rules; compiles schema to Filament form components (with Jalali/date handling). |
| **`ReportsFieldChartsService`** | For app reports: returns “chartable” form fields and value counts per form/field for document data. |
| **`ReminderService`** | Builds reminder logic (e.g. recipient emails, date resolution from document data). |
| **`AuditLogService`** | Logs create/update/delete/view to `AuditLog`. |
| **`TenancyService`** | Tenant/organization helpers if used. |
| **`DocumentObserver`** | Model observer for `Document` (e.g. audit logging on create/update/delete). |

### Model helpers

- **`User::hasPermission($permission)`** – RBAC check.
- **`Form::currentVersion()`**, **`Form::latestPublishedVersion()`** – Active/published form version.
- **`Reminder::getRecipientEmails()`** – Resolves recipient list for reminders.
- **Policies** – `DocumentPolicy`, `FormPolicy`, `OrganizationPolicy`, `UserPolicy`, `PermissionPolicy`, `RolePolicy` enforce authorization.

---

## Localization

- Use **`__('common.key')`** or **`__('permissions.key')`** for translatable strings (keys in `lang/en/common.php`, `lang/fa/common.php`, and permission files).
- Supported locales: **English (en)** and **Persian (fa)**. Jalali dates and Persian UI are supported via `mokhosh/filament-jalali` and language files.

---

## Security and multi-tenancy

- **Tenant isolation:** All tenant-scoped queries must filter by `organization_id` (from auth user’s organization). App panel uses `EnsureTenantAccess` middleware.
- **Admin panel:** Only users with `is_general_manager = 1` (or equivalent) may access `/admin`; enforced by `EnsureGeneralManager` middleware.
- **Policies:** Use policies for documents, forms, organizations, users, roles, and permissions; check permissions in Filament resources and pages where applicable.
- **File access:** Document file download/view routes are protected (auth + authorization); use `FileDownloadController` and ensure policy checks for the document.

---

## Testing and deployment

- **Tests:** PHPUnit; see `tests/`. Run: `php artisan test` (or `./vendor/bin/phpunit`).
- **Deployment:** Run migrations, seed if needed, ensure scheduler is active for `app:process-reminders`, configure queue worker if using queued jobs (e.g. mail). Set `APP_ENV=production`, `APP_DEBUG=false`, and secure `APP_KEY` and `.env`.

---

## Further reading

- [ARCHITECTURE.md](ARCHITECTURE.md) – Models, relationships, panels, and main flows.
- [INSTALL.md](INSTALL.md) – Installation and environment.
- [USER.md](USER.md) – End-user features and workflows.

# Architecture

This document describes the **technical architecture** of **Organization Document Manager**: panels, multi-tenancy, models, relationships, and main flows.

**Audience:** Developers, software architects.

---

## Table of contents

1. [Panels](#panels)
2. [Multi-tenancy](#multi-tenancy)
3. [Models and relationships](#models-and-relationships)
4. [Document and form system](#document-and-form-system)
5. [Chat system](#chat-system)
6. [Reminders system](#reminders-system)
7. [Reports and audit](#reports-and-audit)
8. [Routes and entry points](#routes-and-entry-points)

---

## Panels

The application exposes two Filament panels.

| Panel | Path | Access | Purpose |
|-------|------|--------|---------|
| **Admin** | `/admin` | General Managers only (`EnsureGeneralManager` middleware) | Global config: organizations, users, forms, form versions, audit logs, roles/permissions; admin reports; support chat. |
| **App** | `/app` | Authenticated users with an organization (`EnsureTenantAccess` middleware) | Tenant-scoped: documents, users, roles, chat, reports, reminders, organization settings. |

- **Admin** is the default panel (first route).  
- **App** is tenant-scoped: all list/form/actions are filtered by the current user’s `organization_id`.

---

## Multi-tenancy

- **Tenant = Organization.** Each organization has its own users, documents, reminders, roles, and chat (except support, which is org ↔ admin).
- **Isolation:** Models that belong to an organization have `organization_id`. All queries in the App panel must restrict by `auth()->user()->organization_id`.
- **Middleware:**
  - `EnsureTenantAccess` – Ensures the user has an `organization_id` before allowing access to the App panel.
  - `EnsureGeneralManager` – Ensures the user is a General Manager before allowing access to the Admin panel.
- **Cross-tenant:** General Managers can access Admin and, from App, may have elevated chat access (e.g. support, cross-org private chat) as implemented in the chat logic.

---

## Models and relationships

### Core entities

| Model | Table | Description |
|-------|--------|-------------|
| **Organization** | `organizations` | Tenant: name, slug, contact, settings (e.g. dashboard widget prefs). |
| **User** | `users` | Belongs to one organization; `is_general_manager`, `is_org_admin`; many-to-many to roles. |
| **Role** | `roles` | Per-organization; many-to-many permissions; slug unique per org. |
| **Permission** | `permissions` | Global list (e.g. `documents.view`, `users.edit`); many-to-many to roles. |
| **Form** | `forms` | Global form definition; has many FormVersions. |
| **FormVersion** | `form_versions` | Version of a form; `schema` (JSON), `is_published`, `is_current`. |
| **Document** | `documents` | Belongs to organization, form, form_version; `data` (JSON), `files` (JSON), `title`, `status`, `created_by`. |
| **Reminder** | `reminders` | Belongs to document; `reminder_at`, `is_sent`, `email_to`, field reference. |
| **AuditLog** | `audit_logs` | User actions; `user_id`, `organization_id`, `action`, `model_type`, `model_id`, old/new values. |
| **ChatMessage** | `chat_messages` | General or support; `organization_id`, `user_id`, `message`, `is_support`. |
| **PrivateChat** | `private_chats` | 1‑to‑1; `organization_id`, `sender_id`, `recipient_id`, `message`. |
| **ActivityLog** | `activity_logs` | Optional activity tracking (e.g. file views/edits). |

### Main relationships (summary)

- **Organization** → hasMany Users, Documents, Roles, Reminders, ChatMessages, PrivateChats, AuditLogs.  
- **User** → belongsTo Organization; hasMany Documents (as creator), AuditLogs; belongsToMany Roles.  
- **Role** → belongsTo Organization; belongsToMany Permissions, Users.  
- **Form** → hasMany FormVersions, Documents.  
- **FormVersion** → belongsTo Form; hasMany Documents.  
- **Document** → belongsTo Organization, Form, FormVersion, User (creator); hasMany Reminders.  
- **Reminder** → belongsTo Document.

---

## Document and form system

1. **Form definition (Admin):** Forms and FormVersions are created/edited in Admin. Each FormVersion stores a **schema** (JSON array of blocks: type, data.key, data.label, options, etc.).
2. **Schema compilation:** `FormSchemaService`:
   - Validates schema.
   - Builds validation rules per field.
   - Compiles schema to Filament components (text, number, date, solar date, select, file, etc.) with optional Jalali and date format support.
3. **Document creation (App):** User selects a form (current/published version); the form is rendered dynamically from that version’s schema; submitted values are stored in `documents.data` (JSON). Attachments go in `documents.files`.
4. **Title:** Document title can be generated from a pattern (e.g. from form data) or set manually depending on configuration.
5. **Policies:** `DocumentPolicy` and `FormPolicy` control who can view/create/update/delete documents and forms.

---

## Chat system

- **PrivateChat:** One row per message; `sender_id`, `recipient_id`, `organization_id`. Only the two participants see the thread. General Managers can have cross-org private chats as per implementation.
- **ChatMessage:** Used for **general** (org-wide) and **support** (`is_support = true`). Scoped by `organization_id`. Support messages are visible to General Managers in the Admin **Support Chat** page.
- **Permissions:** e.g. `chat.view`, `chat.send` control access; Organization Admins (or equivalent) can use Support.
- **UI:** App panel: Chat/Messages page (tabs or channels for Direct, General, Support). Admin panel: Support Chat page to reply per organization.

---

## Reminders system

1. **Configuration:** In the form builder (Admin), date/solar-date fields can have “Allow Reminder” enabled. When a document is created/edited, the user can enable a reminder for that field and set optional extra recipients.
2. **Storage:** `Reminder` rows: `document_id`, `reminder_at`, field reference, `email_to`, `is_sent`.
3. **Processing:** Artisan command `app:process-reminders` runs (e.g. every minute). It finds unsent reminders where `reminder_at <= now()`, sends email via `DocumentReminderMail` (and optional queue job), and marks them sent.
4. **Recipients:** Typically document creator + addresses in `email_to`; `ReminderService` and `Reminder::getRecipientEmails()` centralize logic.

---

## Reports and audit

### App panel reports

- **Page:** `App\Filament\App\Pages\Reports`; widgets: stats, documents by form/month/status/creator, and (via `ReportsFieldChartsService`) charts per form field.
- **Export:** Route `app.documents.export` streams CSV of documents (and form data) for the current organization; middleware ensures tenant access.
- All queries are scoped by `organization_id`; when joining tables (e.g. documents + users), use `documents.organization_id` to avoid ambiguous column errors.

### Admin panel reports

- **Page:** `App\Filament\Admin\Pages\Reports`; filters: organization, form, date range. Shows:
  - Document stats and charts (by organization, form, month, status).
  - User activity from **AuditLog** (by action, by user, by organization).
- **AuditLog:** Written by `AuditLogService` / `DocumentObserver`; stores action type, model, user, organization, and optional old/new values.

---

## Routes and entry points

- **Web:** `routes/web.php` – welcome page; authenticated routes for file download/view and app export (`/app/export-documents`).
- **Filament:** Panel providers register Admin (`/admin`) and App (`/app`) with their own login, resources, and pages. No need to list every Filament route here; entry points are `/admin` and `/app`.
- **Console:** `routes/console.php` – schedules `app:process-reminders` every minute.

For installation and environment setup, see [INSTALL.md](INSTALL.md). For development conventions and services, see [ENGINEER.md](ENGINEER.md).

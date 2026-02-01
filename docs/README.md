# Documentation

This directory contains all documentation for **Organization Document Manager**, a multi-tenant document management system built with Laravel and Filament.

---

## Document index

| Document | Audience | Description |
|--------|----------|-------------|
| [**USER.md**](USER.md) | End users (organization members, admins) | How to use the application: documents, reminders, chat, reports, and settings. |
| [**ENGINEER.md**](ENGINEER.md) | Developers, software engineers | Technical overview, architecture, development guidelines, and key code references. |
| [**INSTALL.md**](INSTALL.md) | DevOps, developers | Installation, environment setup, database, and scheduler configuration. |
| [**ARCHITECTURE.md**](ARCHITECTURE.md) | Developers, architects | System architecture: panels, multi-tenancy, models, and main flows. |

---

## Quick links by role

- **I use the app as a member or admin** → Start with [USER.md](USER.md).
- **I develop or deploy the application** → Use [INSTALL.md](INSTALL.md) for setup, then [ENGINEER.md](ENGINEER.md) and [ARCHITECTURE.md](ARCHITECTURE.md) for implementation details.

---

## Conventions

- All docs are in **Markdown** (`.md`).
- **Admin panel** = `/admin` (General Managers only).
- **App panel** = `/app` (organization users; tenant-scoped).
- **Organization** = tenant; data is isolated per organization via `organization_id`.

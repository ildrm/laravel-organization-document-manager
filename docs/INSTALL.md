# Installation and setup

This document describes how to install and configure **Organization Document Manager** from source.

**Audience:** DevOps, developers, or anyone deploying the application.

---

## Table of contents

1. [Prerequisites](#prerequisites)
2. [Clone and install dependencies](#clone-and-install-dependencies)
3. [Environment configuration](#environment-configuration)
4. [Database setup](#database-setup)
5. [Scheduler (reminders)](#scheduler-reminders)
6. [Optional: mail and queue](#optional-mail-and-queue)
7. [Verify installation](#verify-installation)

---

## Prerequisites

- **PHP** 8.2 or higher (extensions: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`)
- **Composer** 2.x
- **Node.js** 18+ and **npm** (if you build frontend assets)
- **MySQL** 5.7+ / **MariaDB** 10.3+ or **SQLite** 3
- **Cron** (or equivalent) for scheduled reminder processing

---

## Clone and install dependencies

```bash
git clone <repository-url>
cd laravel-organization-document-manager
composer install
npm install
```

---

## Environment configuration

1. Copy the example environment file:

   ```bash
   cp .env.example .env
   ```

2. Generate the application key:

   ```bash
   php artisan key:generate
   ```

3. Edit `.env` and set at least:

   | Variable | Description |
   |---------|-------------|
   | `APP_NAME` | Application name (e.g. `Organization Document Manager`) |
   | `APP_URL` | Full URL (e.g. `http://localhost:8000`) |
   | `DB_CONNECTION` | `mysql` or `sqlite` |
   | `DB_*` | Database host, database name, username, password (for MySQL) |

   For **SQLite**, set `DB_CONNECTION=sqlite` and ensure `database/database.sqlite` exists (e.g. `touch database/database.sqlite`).

---

## Database setup

1. Run migrations:

   ```bash
   php artisan migrate
   ```

2. Seed permissions, default organization, and roles:

   ```bash
   php artisan db:seed
   ```

   This runs, in order:

   - **PermissionSeeder** – seeds the `permissions` table
   - **DefaultOrganizationSeeder** – creates a default organization if none exist
   - **RoleSeeder** – creates default roles per organization and assigns permissions

   If no user exists, a test user is created (e.g. `test@example.com`). Create a **General Manager** user in the database (e.g. set `is_general_manager = 1`) to access the admin panel at `/admin`.

---

## Scheduler (reminders)

Reminders are processed by the Artisan command:

```bash
php artisan app:process-reminders
```

Schedule it to run every minute (e.g. via cron):

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Laravel’s scheduler is already configured in `routes/console.php` to run `app:process-reminders` every minute.

---

## Optional: mail and queue

- **Mail:** Configure `MAIL_*` in `.env` so reminder emails are sent. For local testing, `MAIL_MAILER=log` writes emails to the log.
- **Queue:** For background jobs (e.g. sending emails), set `QUEUE_CONNECTION=database` (or `redis`) and run:

  ```bash
  php artisan queue:work
  ```

---

## Verify installation

1. Start the development server:

   ```bash
   php artisan serve
   ```

2. Open `APP_URL` in a browser (e.g. `http://localhost:8000`).

3. Log in:
   - **App panel:** `/app` – use a user that belongs to an organization.
   - **Admin panel:** `/admin` – use a user with `is_general_manager = 1`.

4. Confirm you can create a document, open Reports, and (if configured) that the reminder command runs:

   ```bash
   php artisan app:process-reminders
   ```

For architecture and development details, see [ARCHITECTURE.md](ARCHITECTURE.md) and [ENGINEER.md](ENGINEER.md).

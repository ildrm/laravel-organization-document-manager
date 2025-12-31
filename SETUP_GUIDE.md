# FDMS Setup and Run Guide

## Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL/MariaDB (or SQLite for development)
- Node.js and NPM (for assets, if needed)

## Step-by-Step Setup

### 1. Install Dependencies

```bash
composer install
```

This will install all PHP dependencies including Laravel 11, Filament 4, and Jalali date converter.

### 2. Environment Configuration

Copy the environment file:

```bash
# On Windows (PowerShell)
copy .env.example .env

# On Linux/Mac
cp .env.example .env
```

Edit `.env` and configure:

```env
APP_NAME="FDMS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=en

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fdms
DB_USERNAME=root
DB_PASSWORD=

# Queue Configuration (for reminders)
QUEUE_CONNECTION=database

# Mail Configuration (for reminders and verification)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@fdms.local"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Create Database

Create a MySQL database:

```sql
CREATE DATABASE fdms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or if using SQLite, the file will be created automatically.

### 5. Run Migrations

```bash
php artisan migrate
```

This will create all necessary tables:
- organizations
- users (with organization fields)
- roles, permissions, and pivot tables
- forms, form_versions
- documents
- reminders
- audit_logs

### 6. Create Storage Directories

```bash
# Create private storage directory
mkdir -p storage/app/private/orgs

# Create symbolic link for public storage
php artisan storage:link
```

**Windows PowerShell:**
```powershell
New-Item -ItemType Directory -Force -Path storage/app/private/orgs
php artisan storage:link
```

### 7. Create Initial User (General Manager)

You can create a General Manager user using Tinker:

```bash
php artisan tinker
```

Then run:

```php
$user = \App\Models\User::create([
    'name' => 'General Manager',
    'email' => 'admin@fdms.local',
    'password' => bcrypt('password'),
    'is_general_manager' => true,
    'email_verified_at' => now(),
]);
```

Or create a seeder (recommended):

```bash
php artisan make:seeder GeneralManagerSeeder
```

### 8. Start Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

### 9. Access the Application

- **Admin Panel (General Manager)**: `http://localhost:8000/admin`
- **Tenant Panel (Organization Users)**: `http://localhost:8000/app`

Login with the General Manager credentials created in step 7.

### 10. Start Queue Worker (for Reminders)

In a separate terminal, start the queue worker:

```bash
php artisan queue:work
```

This is required for reminder emails to be sent.

### 11. Setup Scheduler (Optional but Recommended)

For production, add to your crontab:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

For development, you can manually run:

```bash
php artisan schedule:work
```

Or test the reminder command:

```bash
php artisan reminders:dispatch
```

## Quick Start Script

Create a `setup.sh` (Linux/Mac) or `setup.ps1` (Windows) file:

**setup.sh:**
```bash
#!/bin/bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
mkdir -p storage/app/private/orgs
php artisan storage:link
echo "Setup complete! Don't forget to:"
echo "1. Configure .env file"
echo "2. Create a General Manager user"
echo "3. Run: php artisan serve"
```

**setup.ps1:**
```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
New-Item -ItemType Directory -Force -Path storage/app/private/orgs
php artisan storage:link
Write-Host "Setup complete! Don't forget to:"
Write-Host "1. Configure .env file"
Write-Host "2. Create a General Manager user"
Write-Host "3. Run: php artisan serve"
```

## Troubleshooting

### Issue: "Class not found" errors
**Solution:** Run `composer dump-autoload`

### Issue: "Storage link failed"
**Solution:** Delete `public/storage` if it exists, then run `php artisan storage:link` again

### Issue: "Migration errors"
**Solution:** 
- Check database credentials in `.env`
- Ensure database exists
- Try: `php artisan migrate:fresh` (⚠️ This will delete all data)

### Issue: "Queue not working"
**Solution:**
- Ensure `QUEUE_CONNECTION=database` in `.env`
- Run `php artisan queue:work` in a separate terminal
- Check `jobs` table exists (created by migration)

### Issue: "Permission denied" errors
**Solution:**
- Ensure storage directories are writable:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

## Next Steps After Setup

1. **Create Filament Resources** - See `NEXT_STEPS.md`
2. **Create Organization** - Use Admin Panel to create your first organization
3. **Create Organization Admin** - Create a user with `is_org_admin = true`
4. **Create Forms** - Use Admin Panel to create dynamic forms
5. **Create Documents** - Use Tenant Panel to submit documents

## Development Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Refresh migrations (⚠️ deletes data)
php artisan migrate:fresh

# Seed database (when seeders are created)
php artisan db:seed

# Run queue worker
php artisan queue:work

# Run scheduler
php artisan schedule:work

# Test reminder dispatch
php artisan reminders:dispatch

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear
```

## Production Deployment

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Run `php artisan view:cache`
5. Set up supervisor for queue workers
6. Set up cron for scheduler
7. Configure web server (Nginx/Apache)
8. Set proper file permissions

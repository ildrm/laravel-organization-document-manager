# Quick Start Guide

## 🚀 Run the Project in 5 Steps

### Step 1: Install Dependencies (if not done)
```bash
composer install
```

### Step 2: Configure Environment
Edit `.env` file and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fdms
DB_USERNAME=root
DB_PASSWORD=your_password

QUEUE_CONNECTION=database
```

### Step 3: Run Migrations
```bash
php artisan migrate
```

### Step 4: Create Storage Directories
```powershell
# Windows PowerShell
New-Item -ItemType Directory -Force -Path storage/app/private/orgs
php artisan storage:link
```

### Step 5: Start the Server
```bash
php artisan serve
```

**Access the application:**
- Admin Panel: http://localhost:8000/admin
- Tenant Panel: http://localhost:8000/app

## 🔑 Create Your First User (General Manager)

After starting the server, open a new terminal and run:

```bash
php artisan tinker
```

Then paste this:
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@fdms.local',
    'password' => bcrypt('password'),
    'is_general_manager' => true,
    'email_verified_at' => now(),
]);
```

Exit tinker with `exit` or `Ctrl+C`.

Now you can login at http://localhost:8000/admin with:
- **Email:** admin@fdms.local
- **Password:** password

## 📧 Start Queue Worker (for Reminders)

In a separate terminal window:
```bash
php artisan queue:work
```

Keep this running to process reminder emails.

## ✅ Verify Everything Works

1. ✅ Server running: http://localhost:8000
2. ✅ Admin panel accessible: http://localhost:8000/admin
3. ✅ Can login with General Manager account
4. ✅ Queue worker running (optional but recommended)

## 🎯 Next Steps

1. Create an Organization (in Admin Panel)
2. Create an Organization Admin user
3. Create Forms (in Admin Panel)
4. Create Documents (in Tenant Panel)

See `NEXT_STEPS.md` for creating Filament resources.

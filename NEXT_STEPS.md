# Next Steps for FDMS Implementation

## ✅ Completed Foundation

The core foundation of the FDMS system has been implemented:

1. **Database Layer**: All migrations, models, and relationships
2. **Services**: TenancyService, FormSchemaService, ReminderService, AuditLogService
3. **Authorization**: All policies with tenant-scoped access control
4. **Filament Panels**: Admin and Tenant panels with middleware
5. **Reminder System**: Command, job, and scheduling
6. **File Handling**: Secure download controller with tenant scoping
7. **Basic Localization**: English and Persian language files

## 🔨 Immediate Next Steps

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Create Storage Directories
```bash
mkdir -p storage/app/private/orgs
php artisan storage:link
```

### 3. Create Filament Resources

#### Admin Panel Resources (Priority Order)

**OrganizationResource** (`app/Filament/Admin/Resources/OrganizationResource.php`)
```bash
php artisan make:filament-resource Organization --panel=admin
```
- List organizations
- Create/edit organizations
- Manage organization users
- View organization statistics

**FormResource** (`app/Filament/Admin/Resources/FormResource.php`)
```bash
php artisan make:filament-resource Form --panel=admin
```
- Form builder with JSON schema editor
- Version management
- Publish/unpublish forms

**FormVersionResource** (`app/Filament/Admin/Resources/FormVersionResource.php`)
```bash
php artisan make:filament-resource FormVersion --panel=admin
```
- Create new versions
- Edit schema JSON
- Set current version

**UserResource** (`app/Filament/Admin/Resources/UserResource.php`)
```bash
php artisan make:filament-resource User --panel=admin
```
- Manage all users
- Assign General Manager role
- View across organizations

**AuditLogResource** (`app/Filament/Admin/Resources/AuditLogResource.php`)
```bash
php artisan make:filament-resource AuditLog --panel=admin
```
- View all audit logs
- Filter by organization, user, model type
- Export logs

#### Tenant Panel Resources (Priority Order)

**DocumentResource** (`app/Filament/App/Resources/DocumentResource.php`)
```bash
php artisan make:filament-resource Document --panel=app
```
- **Critical**: This is the most complex resource
- Use `FormSchemaService::compileToFilamentComponents()` to render dynamic forms
- Handle file uploads with proper storage paths
- Display document data in readable format

**UserResource** (`app/Filament/App/Resources/UserResource.php`)
```bash
php artisan make:filament-resource User --panel=app
```
- Manage users within organization
- Assign roles
- Scoped to current organization

**RoleResource** (`app/Filament/App/Resources/RoleResource.php`)
```bash
php artisan make:filament-resource Role --panel=app
```
- Create/edit roles
- Assign permissions
- Scoped to current organization

### 4. Implement Dynamic Form Rendering

The `DocumentResource` needs special handling:

```php
use App\Services\FormSchemaService;

// In the form() method:
$formVersion = $this->getRecord()->formVersion ?? $this->getFormVersion();
$schema = $formVersion->schema ?? [];

$formSchemaService = app(FormSchemaService::class);
$components = $formSchemaService->compileToFilamentComponents(
    $schema,
    auth()->user()->language_preference ?? 'en'
);

return $form->schema($components);
```

### 5. Add Tenant Scoping to Resources

All tenant panel resources should scope queries:

```php
protected function getTableQuery(): Builder
{
    $query = parent::getTableQuery();
    
    if (!auth()->user()->isGeneralManager()) {
        $query->where('organization_id', auth()->user()->organization_id);
    }
    
    return $query;
}
```

### 6. Create Organization Registration

**Controller**: `app/Http/Controllers/Auth/RegisterOrganizationController.php`
- Public registration form
- Create organization
- Create default admin user
- Send verification email

**Route**: Add to `routes/web.php`
```php
Route::get('/register', [RegisterOrganizationController::class, 'showRegistrationForm']);
Route::post('/register', [RegisterOrganizationController::class, 'register']);
```

### 7. Enhance Localization

Add more translation files:
- `lang/en/resources.php` - Resource labels
- `lang/fa/resources.php` - Resource labels (Persian)
- `lang/en/navigation.php` - Navigation items
- `lang/fa/navigation.php` - Navigation items (Persian)

### 8. Add RTL Support

Create `resources/css/rtl.css`:
```css
[dir="rtl"] {
    direction: rtl;
    text-align: right;
}
```

Configure Filament panels to use RTL when language is 'fa'.

### 9. Create Seeders

**DatabaseSeeder**: Create initial data
- Default permissions
- System roles
- Sample General Manager user

**PermissionSeeder**: Seed common permissions
- `documents.view`
- `documents.create`
- `documents.edit`
- `documents.delete`
- `users.view`
- `users.create`
- `users.edit`
- `users.delete`
- `roles.view`
- `roles.create`
- `roles.edit`
- `roles.delete`

### 10. Testing

Create tests in `tests/Feature/`:
- `TenantIsolationTest.php` - Ensure no cross-tenant access
- `DocumentPolicyTest.php` - Test authorization
- `FormSchemaServiceTest.php` - Test schema compilation
- `ReminderServiceTest.php` - Test reminder creation

## Configuration Checklist

- [ ] Set `QUEUE_CONNECTION=database` in `.env`
- [ ] Set up cron for scheduler: `* * * * * php /path/to/artisan schedule:run`
- [ ] Configure mail settings in `.env`
- [ ] Set `APP_LOCALE` based on default language
- [ ] Create private storage directory

## Key Files to Review

1. `app/Services/FormSchemaService.php` - Dynamic form compilation
2. `app/Policies/*` - All authorization policies
3. `app/Models/*` - All models with relationships
4. `database/migrations/*` - All database schema

## Important Notes

- **General Manager**: Has `is_general_manager = true`, bypasses all checks
- **Organization Admin**: Has `is_org_admin = true`, full access within org
- **Tenant Scoping**: Always check `organization_id` in queries
- **Form Versions**: Documents reference `form_version_id` for data integrity
- **File Storage**: Use `private` disk, paths: `orgs/{org_id}/documents/{doc_id}/`
- **Reminders**: Scheduled command runs every minute, uses queue for emails

## Support

Refer to `IMPLEMENTATION_STATUS.md` for detailed architecture decisions and completed components.

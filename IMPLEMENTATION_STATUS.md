# FDMS Implementation Status

## Architecture Decisions

### 1. Tenancy Approach
- **Strategy**: Organization-based multi-tenancy
- **Implementation**: `organization_id` foreign key on all tenant-scoped models
- **General Manager**: Bypasses tenant scoping (can access all organizations)
- **Organization Users**: Scoped to their `organization_id`
- **Service**: `TenancyService` provides helper methods for tenant resolution and scoping

### 2. RBAC Approach
- **Implementation**: Custom roles and permissions system (not using Spatie package per requirements)
- **Structure**: 
  - `roles` table (tenant-scoped)
  - `permissions` table (global)
  - `permission_role` pivot table
  - `role_user` pivot table
- **Authorization**: Policies enforce access control
- **General Manager**: Bypasses all permission checks
- **Organization Admin**: Full permissions within their organization
- **Custom Roles**: Permissions assigned via `permission_role` table

### 3. Schema/Versioning Approach
- **Forms Table**: Stores form metadata (name, slug, description)
- **Form Versions Table**: Stores versioned schema JSON
- **Documents**: Reference `form_version_id` to maintain data integrity
- **Versioning Rules**: 
  - Only one `is_current` version per form
  - Old versions remain readable for existing documents
  - New versions can be published without breaking existing documents

### 4. Reminder Approach
- **Storage**: `reminders` table tracks all reminders
- **Scheduling**: Scheduled command runs periodically to dispatch due reminders
- **Queue**: Uses Laravel database queue for email dispatch
- **Idempotency**: `is_sent` flag prevents duplicate emails

### 5. Date Handling
- **Storage**: All dates stored as Gregorian (UTC) in database
- **Jalali Conversion**: `FormSchemaService` handles conversion using `morilog/jalali`
- **Display**: Format based on user's `language_preference` (en/fa)

### 6. File Storage
- **Strategy**: Private disk with organization-scoped directories
- **Path Pattern**: `orgs/{org_id}/documents/{document_id}/...`
- **Access**: Secure download controller with authorization checks

## Completed Components

### ✅ Database Layer
- [x] All migrations created and configured
- [x] All models with relationships
- [x] Soft deletes on organizations, forms, form_versions, documents, roles

### ✅ Services
- [x] `TenancyService` - Tenant resolution and scoping
- [x] `FormSchemaService` - Schema validation and Filament component compilation
- [x] `ReminderService` - Reminder creation and dispatch
- [x] `AuditLogService` - Audit logging

### ✅ Authorization
- [x] All policies created (Organization, Document, Form, User, Role)
- [x] Tenant-scoped authorization
- [x] General Manager bypass logic

### ✅ Filament Panels
- [x] Admin Panel (`/admin`) - General Manager only
- [x] Tenant Panel (`/app`) - Organization users
- [x] Middleware for access control

### ✅ Middleware
- [x] `EnsureGeneralManager` - Restricts admin panel to GM
- [x] `EnsureTenantAccess` - Ensures org users have organization

## Remaining Implementation Tasks

### 🔲 Filament Resources (High Priority)

#### Admin Panel Resources
1. **OrganizationResource** (`app/Filament/Admin/Resources/OrganizationResource.php`)
   - List, create, edit, delete organizations
   - Manage organization users
   - View organization documents

2. **FormResource** (`app/Filament/Admin/Resources/FormResource.php`)
   - Form builder with visual editor
   - Version management
   - Schema JSON editor with validation

3. **FormVersionResource** (`app/Filament/Admin/Resources/FormVersionResource.php`)
   - Create new versions
   - Publish/unpublish versions
   - Set current version

4. **UserResource** (`app/Filament/Admin/Resources/UserResource.php`)
   - Manage all users across organizations
   - Assign General Manager role

5. **AuditLogResource** (`app/Filament/Admin/Resources/AuditLogResource.php`)
   - View all audit logs
   - Filter by organization, user, model type

#### Tenant Panel Resources
1. **DocumentResource** (`app/Filament/App/Resources/DocumentResource.php`)
   - Dynamic form rendering based on `form_version.schema`
   - File upload handling
   - List with filters (date range, form, status)
   - Detail view with formatted data

2. **UserResource** (`app/Filament/App/Resources/UserResource.php`)
   - Manage users within organization
   - Assign roles

3. **RoleResource** (`app/Filament/App/Resources/RoleResource.php`)
   - Create/edit roles
   - Assign permissions

### 🔲 Reminder System
1. **Command**: `app/Console/Commands/DispatchReminders.php`
   - Scheduled to run every minute
   - Fetches due reminders
   - Dispatches email jobs

2. **Job**: `app/Jobs/SendReminderEmail.php`
   - Sends reminder emails
   - Marks reminder as sent

3. **Schedule**: Add to `app/Console/Kernel.php`

### 🔲 File Handling
1. **Controller**: `app/Http/Controllers/FileDownloadController.php`
   - Secure file downloads
   - Tenant scoping
   - Authorization checks

2. **Route**: Add route for file downloads

### 🔲 Localization
1. **Language Files**:
   - `lang/en/` - English translations
   - `lang/fa/` - Persian translations
   - Key files: `common.php`, `navigation.php`, `resources.php`

2. **RTL Support**:
   - CSS for RTL layout
   - Filament panel RTL configuration

3. **Language Switcher**:
   - User profile setting
   - Header dropdown component

### 🔲 Organization Registration
1. **Controller**: `app/Http/Controllers/Auth/RegisterOrganizationController.php`
2. **Form Request**: Validation for organization signup
3. **Route**: Public registration route
4. **Email Verification**: Laravel's built-in verification

### 🔲 Testing
1. **Feature Tests**:
   - Tenant isolation tests
   - Policy authorization tests
   - Dynamic form rendering tests

2. **Unit Tests**:
   - Service tests
   - Date conversion tests

## Commands to Run

```bash
# Run migrations
php artisan migrate

# Create storage link
php artisan storage:link

# Set up queue (if using database queue)
php artisan queue:work

# Set up scheduler (add to crontab)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Environment Configuration

Add to `.env`:
```
QUEUE_CONNECTION=database
APP_LOCALE=en
```

## Next Steps

1. Create Filament resources (start with Organization and Document)
2. Implement reminder command and job
3. Create file download controller
4. Add localization files
5. Create organization registration
6. Write tests

## Notes

- All models use proper relationships and type hints
- Policies are registered in `AppServiceProvider` (add if not already)
- Form schema uses JSON structure with field definitions
- Jalali date conversion handled in `FormSchemaService`
- Phone validation is basic (can be enhanced with libphonenumber later)

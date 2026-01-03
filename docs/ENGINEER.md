# Software Engineer Documentation

## Project Overview
This project is a multi-tenant Document Management System built with Laravel and Filament 4. It features dynamic form building, document automation with reminders, and a multi-mode chat system.

## Core Technologies
- **Framework**: Laravel 11
- **UI Framework**: Filament 4 (including Schemas)
- **Frontend**: Livewire 3, Tailwind CSS
- **Database**: MySQL/MariaDB
- **Date Handling**: Carbon (with Jalali/Solar date support)

## Architecture

### 1. Document System
Documents are based on dynamic forms.
- **Form Models**: `Form` and `FormVersion`.
- **Schema Storage**: JSON in `form_versions.schema`.
- **Dynamic Rendering**: `FormSchemaService` translates JSON schemas into Filament components.
- **Document Storage**: Data is stored in `documents.data` (JSON).

### 2. Chat System
Supports Private, General, and Support modes.
- **PrivateChat**: 1-on-1 messaging between organization members.
- **ChatMessage**: General organization-wide messages and Support messages (flagged with `is_support`).
- **Livewire Components**:
  - `App\Filament\App\Pages\Chat`: Main chat interface for users.
  - `App\Filament\Admin\Pages\SupportChat`: Interface for administrators to handle support queries for all organizations. Features organization search, last message previews, and real-time polling.

### 3. Chat Permissions & Isolation
The chat system enforces strict multi-tenant isolation with specialized access for General Managers:
- **Organization Users**: Restricted to chatting with members of their own organization (Direct & General).
- **Organization Admins**: Same as users, plus access to the "Support" channel to communicate with General Admins. All Support messages are scoped to their `organization_id`.
- **General Admins (Global)**: 
  - In the **Admin Panel**, they can manage support threads for all organizations.
  - In the **App Panel**, they can search for and start Private Chats with any user across any organization. Isolation is bypassed for GMs to allow cross-tenant communication.

### 4. Reminders System
Automated reminders based on date fields in documents.
- **Trigger**: "Allow Reminder" attribute on Date/Solar Date fields in Form Builder.
- **Processing**: `app:process-reminders` console command.
- **Email Delivery**: `DocumentReminderMail` sends notifications to the document creator and specified recipients.

## Database Schema Highlights

### `private_chats`
- `organization_id`: Tenant isolation.
- `sender_id`, `recipient_id`: Links to users.
- `message`: Text content.
- `is_read`: Boolean status.

### `reminders`
- `document_id`: Associated document.
- `reminder_at`: Target datetime for the reminder.
- `is_sent`: Status flag.
- `email_to`: CSV or JSON list of additional recipients.

## Key Services & Utilities
- **`FormSchemaService`**: Central logic for converting JSON schemas to Filament UI.
- **Model Scopes/Helpers**:
  - `User::hasPermission($permission)`: RBAC check.
  - `Form::latestPublishedVersion()`: Utility for finding the active form version.
  - `Reminder::getRecipientEmails()`: Logic for consolidating reminder recipients.

## Development Guidelines
- **Filament 4**: Always use `Filament\Schemas\Schema` for form/infolist definitions.
- **Localization**: Use `__('common.key')` for translations. The system supports English (EN) and Persian (FA).
- **Security**: Tenant isolation is strictly enforced via `organization_id` on all major models.

## Deployment & Setup
1. Run `composer install`.
2. Configure `.env` with database and mail settings.
3. Run `php artisan migrate --seed`.
4. Schedule `php artisan app:process-reminders` to run every minute via Cron.

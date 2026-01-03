# Organization Document Manager

A multi-tenant document management system built with Laravel and Filament 4.

## Features
- **Dynamic Form Builder**: Create complex forms with multiple versions.
- **Document Automation**: Generate documents from forms with custom title patterns.
- **Automated Reminders**: Set email reminders for important dates within documents.
- **Multi-mode Chat**: Private, General, and Support chat systems.
- **Multi-tenancy**: Strictly isolated data between organizations.
- **Localization**: Full support for English and Persian (Jalali dates included).

## Documentation
- [Software Engineer Documentation](docs/ENGINEER.md)
- [User Guide](docs/USER.md)

## Getting Started
1. Clone the repository.
2. Install dependencies: `composer install && npm install`.
3. Set up environment: `cp .env.example .env` and configure your database.
4. Run migrations and seeders: `php artisan migrate --seed`.
5. Start the server: `php artisan serve`.

## License
[MIT](LICENSE)

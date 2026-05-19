# Dynamic Form Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/DynamicForm`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\DynamicForm\\": "Modules/DynamicForm/"
    }
  }
}
```
Rebuild autoload:
```bash
composer dump-autoload
```

## Providers
Register the Service Provider so the module's migrations and views load.

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\DynamicForm\DynamicFormServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('Modules/DynamicForm/Filament/Resources'), for: 'Modules\DynamicForm\Filament\Resources')
->discoverPages(in: base_path('Modules/DynamicForm/Filament/Pages'), for: 'Modules\DynamicForm\Filament\Pages')
```
Note: The module also registers resources via `DynamicFormServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/DynamicForm/Database/Migrations
```

## Seeder
Seed sample Dynamic Form data:
```bash
php artisan db:seed --class=Modules\\DynamicForm\\Database\\Seeders\\FormSeeder
```

## Features

✅ **Dynamic Form Builder**
- Create forms using SurveyJS JSON schema
- Paste JSON schema from [SurveyJS Form Builder](https://surveyjs.io/create-free-survey)
- Support for 20+ question types (text, choice, rating, file upload, etc.)
- Multi-page forms with conditional logic

✅ **Public Form Access**
- Public URL using slug: `/form/{slug}`
- Responsive design
- Support for file uploads
- Real-time validation

✅ **Form Settings**
- Require login before accessing form
- Collect email automatically
- Allow/restrict multiple submissions
- Set form expiration date

✅ **Submission Management**
- View all form submissions
- View submission details with form rendered in read-only mode
- Track responder information (name, email, IP address)

## Usage

### Creating a Form
1. Go to Admin Panel → Dynamic Form → Forms
2. Click "Create Form"
3. Fill in title, description, and settings
4. In the "Form Builder" section, paste your SurveyJS JSON schema
5. You can create the schema at [SurveyJS Form Builder](https://surveyjs.io/create-free-survey)
6. Save the form

### Accessing Public Form
- Public URL format: `/form/{slug}`
- Example: `/form/tanya-jawab`
- The form will be rendered using SurveyJS library

### Viewing Submissions
1. Go to Admin Panel → Dynamic Form → Submissions
2. Click "View" on any submission to see the filled form in read-only mode

## Routes

- Admin: `/admin/forms` - CRUD Forms
- Admin: `/admin/form-submissions` - View Submissions
- Public: `/form/{slug}` - Public form page
- Public: `/form/{slug}/submit` - Submit form (POST)

DewaFilament by [dewakoding](https://dewakoding.com)

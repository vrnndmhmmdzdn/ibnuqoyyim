# Forum Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module into `Modules/Forum`.

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\Forum\\": "Modules/Forum/"
    }
  }
}
```
Rebuild autoload:
```bash
composer dump-autoload
```

## Providers
Register the Service Provider so the module's migrations, views, policies, and Livewire components load.

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\Forum\ForumServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources and pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('Modules/Forum/Filament/Resources'), for: 'Modules\Forum\Filament\Resources')
->discoverPages(in: base_path('Modules/Forum/Filament/Pages'), for: 'Modules\Forum\Filament\Pages')
```

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/Forum/Database/Migrations
```

## Seeder
Seed sample Forum questions and comments:
```bash
php artisan db:seed --class="Modules\\Forum\\Database\\Seeders\\ForumSeeder"
```

## What You Get
- Filament page: **Forum** (`/admin/forum`) with:
  - Question list with search and pagination
  - Create Question modal (with Filament Rich Editor)
  - Flat, responsive UI (light/dark mode)
- Filament page: **Forum Question Detail** (`/admin/forum/questions/{slug}`) with:
  - Thread view
  - Reply form (Filament Rich Editor)
  - Comment timeline
- Filament resources:
  - **Forum Questions**
  - **Forum Comments**

DewaFilament by [dewakoding](https://dewakoding.com)

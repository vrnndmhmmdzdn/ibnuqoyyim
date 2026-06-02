# JurnalGuru Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/JurnalGuru`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\JurnalGuru\\": "Modules/JurnalGuru/"
    }
  }
}
```
Rebuild autoload:
```bash
composer dump-autoload
```

## Providers
Register the Service Provider so the module’s migrations and views load.

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\JurnalGuru\JurnalGuruServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/JurnalGuru/Filament/Resources'), for: 'Modules\JurnalGuru\Filament\Resources')
->discoverPages(in: base_path('modules/JurnalGuru/Filament/Pages'), for: 'Modules\JurnalGuru\Filament\Pages')
```
Note: The module also registers resources via `JurnalGuruServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/JurnalGuru/Database/Migrations
```

## Seeder
Seed sample JurnalGuru data:
```bash
php artisan db:seed --class="Modules\\JurnalGuru\\Database\\Seeders\\JurnalGuruSeeder"
```

DewaFilament by [dewakoding](https://dewakoding.com)


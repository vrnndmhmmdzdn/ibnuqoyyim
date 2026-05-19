# Angkatan Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/Angkatan`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\Angkatan\\": "modules/Angkatan/"
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
    Modules\Angkatan\AngkatanServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/Angkatan/Filament/Resources'), for: 'Modules\Angkatan\Filament\Resources')
->discoverPages(in: base_path('modules/Angkatan/Filament/Pages'), for: 'Modules\Angkatan\Filament\Pages')
```
Note: The module also registers resources via `AngkatanServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.


## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/Angkatan/Database/Migrations
```

## Seeder
Seed sample Angkatan data:
```bash
php artisan db:seed --class="Modules\\Angkatan\\Database\\Seeders\\AngkatanSeeder"
```

DewaFilament by [dewakoding](https://dewakoding.com)
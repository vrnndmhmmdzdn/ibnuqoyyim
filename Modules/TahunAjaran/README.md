# TahunAjaran Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/TahunAjaran`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\TahunAjaran\\": "modules/TahunAjaran/"
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
    Modules\TahunAjaran\TahunAjaranServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/TahunAjaran/Filament/Resources'), for: 'Modules\TahunAjaran\Filament\Resources')
->discoverPages(in: base_path('modules/TahunAjaran/Filament/Pages'), for: 'Modules\TahunAjaran\Filament\Pages')
```
Note: The module also registers resources via `TahunAjaranServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.


## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/TahunAjaran/Database/Migrations
```

## Seeder
Seed sample TahunAjaran data:
```bash
php artisan db:seed --class="Modules\\TahunAjaran\\Database\\Seeders\\TahunAjaranSeeder"
```

DewaFilament by [dewakoding](https://dewakoding.com)
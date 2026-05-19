# Siswa Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/Siswa`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\Siswa\\": "modules/Siswa/"
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
    Modules\Siswa\SiswaServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/Siswa/Filament/Resources'), for: 'Modules\Siswa\Filament\Resources')
->discoverPages(in: base_path('modules/Siswa/Filament/Pages'), for: 'Modules\Siswa\Filament\Pages')
```
Note: The module also registers resources via `SiswaServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.


## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/Siswa/Database/Migrations
```

## Seeder
Seed sample Siswa data:
```bash
php artisan db:seed --class="Modules\\Siswa\\Database\\Seeders\\SiswaSeeder"
```

DewaFilament by [dewakoding](https://dewakoding.com)
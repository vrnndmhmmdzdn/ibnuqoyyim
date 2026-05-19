# MataPelajaran Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/MataPelajaran`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\MataPelajaran\\": "modules/MataPelajaran/"
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
    Modules\MataPelajaran\MataPelajaranServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/MataPelajaran/Filament/Resources'), for: 'Modules\MataPelajaran\Filament\Resources')
->discoverPages(in: base_path('modules/MataPelajaran/Filament/Pages'), for: 'Modules\MataPelajaran\Filament\Pages')
```
Note: The module also registers resources via `MataPelajaranServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.


## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/MataPelajaran/Database/Migrations
```

## Seeder
Seed sample MataPelajaran data:
```bash
php artisan db:seed --class="Modules\\MataPelajaran\\Database\\Seeders\\MataPelajaranSeeder"
```

DewaFilament by [dewakoding](https://dewakoding.com)
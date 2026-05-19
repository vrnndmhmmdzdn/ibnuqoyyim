# Donation Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/Donation`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\Donation\\": "Modules/Donation/"
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
    Modules\Donation\DonationServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/Donation/Filament/Resources'), for: 'Modules\Donation\Filament\Resources')
```
Note: The module also registers widgets via `DonationServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/Donation/Database/Migrations
```

## Seeder
Seed sample Donation data:
```bash
php artisan db:seed --class="Modules\\Donation\\Database\\Seeders\\DonationSeeder"
```

## Public Routes
- `GET /donation`
- `GET /donation/{campaign:slug}`
- `GET /donation/{campaign:slug}/donate`
- `GET /donation/checkout/{orderId}`
- `GET /donation/status`
- `GET /donation/status/{orderId}`

## Notes
- Donation web pages use Livewire page components (no web controllers).
- Midtrans webhook handling is provided by `Module Midtrans`.

DewaFilament by [dewakoding](https://dewakoding.com)

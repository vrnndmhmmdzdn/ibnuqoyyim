# DashboardBuilder Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/DashboardBuilder`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\DashboardBuilder\\": "Modules/DashboardBuilder/"
    }
  }
}
```
Rebuild autoload:
```bash
composer dump-autoload
```

## Providers
Register the Service Provider so the module's migrations, routes, and views load.

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\DashboardBuilder\DashboardBuilderServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover the module resource in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('Modules/DashboardBuilder/Filament/Resources'), for: 'Modules\DashboardBuilder\Filament\Resources')
->discoverPages(in: base_path('Modules/DashboardBuilder/Filament/Pages'), for: 'Modules\DashboardBuilder\Filament\Pages')
```

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/DashboardBuilder/Database/Migrations
```

## What This Module Provides
- Visual dashboard builder inside Filament
- Multi-chart dashboards with canvas-style layout
- Chart configuration stored as JSON per chart
- Table-based chart source picker with grouping and aggregate options
- Internal preview page for admin users
- Public publish flow with unique UUID link
- Optional login requirement for published dashboards

## Main Flow
1. Create a dashboard from the **Dashboard Builder** list page.
2. Open the builder page and add one or more charts.
3. Configure chart type, table/entity, grouping, aggregate, width, ratio, and limit.
4. Save the layout.
5. Use **Preview** for internal admin preview.
6. Use **Publish** to generate a public UUID link, with optional login protection.

## Routes
This module loads its own web routes automatically from the service provider.

Main routes:
- Admin preview: `/admin/report/{dashboard}/preview`
- Published report: `/report/{uuid}`

## Notes
- Published links use a UUID stored in the dashboard record.
- If **Require Login** is enabled during publish, the published page redirects through the Filament login flow first.
- The chart builder only reads tables from the current project database/schema.
- Chart rendering is shared between builder preview, admin preview, and published page.

DewaFilament by [dewakoding](https://dewakoding.com)

# AuditLog Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/AuditLog`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\AuditLog\\": "Modules/AuditLog/"
    }
  }
}
```
Rebuild autoload:
```bash
composer dump-autoload
```

## Providers
Register the Service Provider so the module's migrations and Filament resource load.

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\AuditLog\AuditLogServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('modules/AuditLog/Filament/Resources'), for: 'Modules\AuditLog\Filament\Resources')
```
Note: The module also registers resources via `AuditLogServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/AuditLog/Database/Migrations
```

## Seeder
Seed sample audit data:
```bash
php artisan db:seed --class="Modules\\AuditLog\\Database\\Seeders\\AuditLogSeeder"
```

## Configuration
Publish config (optional):
```bash
php artisan vendor:publish --tag=auditlog-config
```

Main options in `config/auditlog.php`:
- `enabled`: Turn logging on/off.
- `auto_discover_models`: Auto-observe models in `app/Models` and `Modules/*/Models`.
- `include_models`: Explicit model list when auto discovery is disabled.
- `exclude_models`: Model list to skip.
- `model_event_rules`: Per-model event rules (`created`, `updated`, `deleted`, `restored`).
- `ignored_attributes`: Attributes excluded from payload.
- `masked_attributes`: Exact field names to mask (e.g. `password`, `token`, `secret`).
- `masked_attribute_patterns`: Regex patterns for sensitive keys.
- `mask_placeholder`: Replacement text for masked values.
- `log_in_console`: Log events from console commands/seeding.

## How It Works
- Tracks Eloquent events: `created`, `updated`, `deleted`, `restored`.
- Captures actor (`auth()`), request metadata (IP/method/URL/user-agent), and before/after values.
- Provides read-only Filament resource: **Audit Logs** under navigation group **System**.
- Includes a built-in **Diff Viewer** on the detail page for per-field before/after changes.
- Shows **table size** and **total rows** in the Audit Logs list header.
- Includes advanced filters: date range, actor type/id, method, record id, and URL contains.

## Database Growth Notice
- Audit logging can significantly increase database size over time, especially on high-traffic apps or models with frequent updates.
- Plan storage capacity, indexing, and cleanup strategy early to avoid performance degradation.

## Per-Model / Per-Event Rules Example
```php
'model_event_rules' => [
    App\Models\User::class => ['updated', 'deleted'],
    Modules\Donation\Models\DonationCampaign::class => ['created', 'updated'],
    '*' => ['created', 'updated', 'deleted', 'restored'],
],
```

DewaFilament by [dewakoding](https://dewakoding.com)

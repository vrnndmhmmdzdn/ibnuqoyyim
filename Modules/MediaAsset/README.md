# Media Asset Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module into `Modules/MediaAsset`.

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\MediaAsset\\": "Modules/MediaAsset/"
    }
  }
}
```
Rebuild autoload:
```bash
composer dump-autoload
```

## Required Package
This module depends on Spatie Media Library. Install it before running migrations.

```bash
composer require spatie/laravel-medialibrary:^11
```

## Providers
Register the Service Provider so the module's migrations and views load.

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\MediaAsset\MediaAssetServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources and pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('Modules/MediaAsset/Filament/Resources'), for: 'Modules\MediaAsset\Filament\Resources')
->discoverPages(in: base_path('Modules/MediaAsset/Filament/Pages'), for: 'Modules\MediaAsset\Filament\Pages')
```

## Migrations
Run the module migrations:
```bash
php artisan migrate --path=Modules/MediaAsset/Database/Migrations
```

## Seeder
Seed sample media images:
```bash
php artisan db:seed --class="Modules\\MediaAsset\\Database\\Seeders\\MediaAssetSeeder"
```

## What You Get
- Filament page: **Media Gallery** (`/admin/media-gallery`) with:
  - Folder browser
  - Multi-image upload
  - Preview, move, copy link, and delete actions
- Filament resource: **Media Assets** (`/admin/media-assets`) with:
  - Table view for uploaded media
  - Folder filter and disk filter
  - Edit, preview, move folder, and delete actions
- Built-in image handling via Spatie Media Library:
  - Public URL generation
  - Thumbnail conversion
  - Single-file media collection per asset

## Notes
- The seeder downloads demo images from `picsum.photos`, so an internet connection is required when running it.
- Demo images in the seeder are powered by [Picsum Photos](https://picsum.photos/).
- Uploaded files use the `public` disk by default.
- Temporary uploads are stored in `media-assets/tmp`.

DewaFilament by [dewakoding](https://dewakoding.com)

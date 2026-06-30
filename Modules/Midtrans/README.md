# Midtrans Module — Quick Setup (with notes)

## Initial Setup
1. Create the `Modules` folder at the project root (same level as `app`).
```bash
mkdir -p Modules
```
2. Copy the module you want into `Modules` (e.g., copy `Modules/Midtrans`).

## Composer (PSR-4)
Autoload the module namespace so Composer resolves classes.

File: `composer.json`
```json
{
  "autoload": {
    "psr-4": {
      "Modules\\Midtrans\\": "Modules/Midtrans/"
    }
  }
}
```
Rebuild autoload:
```bash
composer dump-autoload
```

## Providers
Register the Service Provider so the module's migrations and views load.

File: `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    Modules\Midtrans\MidtransServiceProvider::class,
];
```

## Admin Panel (Filament)
Discover module resources & pages in the admin panel.

File: `app/Providers/Filament/AdminPanelProvider.php`
```php
->discoverResources(in: base_path('Modules/Midtrans/Filament/Resources'), for: 'Modules\Midtrans\Filament\Resources')
->discoverPages(in: base_path('Modules/Midtrans/Filament/Pages'), for: 'Modules\Midtrans\Filament\Pages')
```
Note: The module also registers resources via `MidtransServiceProvider` during `Filament::serving`. Avoid duplicates if using both discovery and provider registration.

## Migrations
Module-only:
```bash
php artisan migrate --path=Modules/Midtrans/Database/Migrations
```

## Seeder
Seed sample Midtrans credential:
```bash
php artisan db:seed --class="Modules\\Midtrans\\Database\\Seeders\\MidtransSeeder"
```

## Configuration
All configuration is done via **Filament Admin Panel**:
1. Navigate to **Midtrans > Midtrans Credentials**
2. Add your Midtrans credentials (Server Key, Client Key, Merchant ID)
3. Set credential as **Active**

## Usage

### Create Snap Transaction
```php
use Modules\Midtrans\Services\MidtransService;

$midtransService = app(MidtransService::class);

$transaction = $midtransService->createSnapTransaction(
    orderId: 'ORDER-' . time(),
    grossAmount: 100000,
    itemDetails: [
        ['id' => 'PROD-001', 'price' => 100000, 'quantity' => 1, 'name' => 'Product']
    ],
    customerDetails: [
        'first_name' => 'John',
        'email' => 'john@example.com',
        'phone' => '08123456789'
    ]
);

return redirect($transaction->snap_url);
```

### Check Transaction Status
```php
$status = $midtransService->getTransactionStatus('ORDER-123456');
```

### Cancel Transaction
```php
$midtransService->cancelTransaction('ORDER-123456');
```

## Routes
- `POST /midtrans/notification` - Webhook notification handler
- `GET /midtrans/finish` - Payment finish redirect
- `GET /midtrans/unfinish` - Payment unfinish redirect
- `GET /midtrans/error` - Payment error redirect
- `POST /midtrans/check-status/{orderId}` - AJAX status check

## Transaction Status Tracking

The `updated_via` field tracks how transaction status was updated:

| Value | Description |
|-------|-------------|
| `webhook` | Updated by Midtrans webhook notification (most reliable) |
| `callback` | Updated when user redirected to finish/unfinish/error page |
| `ajax` | Updated via JavaScript call after payment popup closed |
| `manual` | Updated manually by admin |
| `legacy` | Transaction existed before tracking was implemented |

**Note:** In production with webhook properly configured, most transactions should show `webhook`. If you see many `callback` or `ajax` entries, check your webhook configuration in Midtrans Dashboard.

DewaFilament by [dewakoding](https://dewakoding.com)

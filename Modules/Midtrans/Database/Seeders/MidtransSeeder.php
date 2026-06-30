<?php

namespace Modules\Midtrans\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Midtrans\Models\MidtransCredential;

class MidtransSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sandbox credential (for testing)
        MidtransCredential::create([
            'name' => 'Sandbox Testing',
            'environment' => 'sandbox',
            'server_key' => 'SB-Mid-server-YOUR_SERVER_KEY_HERE',
            'client_key' => 'SB-Mid-client-YOUR_CLIENT_KEY_HERE',
            'merchant_id' => 'YOUR_MERCHANT_ID',
            'is_sanitized' => true,
            'is_3ds' => true,
            'notification_url' => url('/midtrans/notification'),
            'finish_url' => url('/midtrans/finish'),
            'unfinish_url' => url('/midtrans/unfinish'),
            'error_url' => url('/midtrans/error'),
            'is_active' => true,
            'notes' => 'Credential untuk testing di sandbox environment',
        ]);

        // Create production credential (inactive by default)
        MidtransCredential::create([
            'name' => 'Production Live',
            'environment' => 'production',
            'server_key' => 'Mid-server-YOUR_PRODUCTION_SERVER_KEY_HERE',
            'client_key' => 'Mid-client-YOUR_PRODUCTION_CLIENT_KEY_HERE',
            'merchant_id' => 'YOUR_MERCHANT_ID',
            'is_sanitized' => true,
            'is_3ds' => true,
            'notification_url' => url('/midtrans/notification'),
            'finish_url' => url('/midtrans/finish'),
            'unfinish_url' => url('/midtrans/unfinish'),
            'error_url' => url('/midtrans/error'),
            'is_active' => false,
            'notes' => 'Credential untuk production environment (LIVE)',
        ]);
    }
}

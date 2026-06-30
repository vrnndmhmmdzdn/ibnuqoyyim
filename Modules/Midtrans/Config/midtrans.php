<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration untuk Midtrans Payment Gateway
    |
    */

    'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'), // sandbox atau production
    
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
    
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    /*
    |--------------------------------------------------------------------------
    | Demo Mode
    |--------------------------------------------------------------------------
    |
    | Jika true, API credentials (server_key, client_key, merchant_id) akan
    | di-lock setelah pertama kali diinput dan tidak bisa diubah lagi.
    | Set false untuk development/penggunaan normal.
    |
    */
    'demo_mode' => env('MIDTRANS_DEMO_MODE', false),

    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', ''),    'finish_url' => env('MIDTRANS_FINISH_URL', ''),
    
    'unfinish_url' => env('MIDTRANS_UNFINISH_URL', ''),
    
    'error_url' => env('MIDTRANS_ERROR_URL', ''),
];

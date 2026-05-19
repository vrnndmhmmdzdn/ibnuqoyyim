<?php

return [
    'enabled' => env('AUDITLOG_ENABLED', false),

    // If false, only models listed in "include_models" will be observed.
    'auto_discover_models' => env('AUDITLOG_AUTO_DISCOVER_MODELS', false),

    // Relative directories/patterns from base_path().
    'model_paths' => [
        'app/Models',
        'Modules/*/Models',
    ],

    // Explicitly include models (useful when auto discovery is disabled).
    'include_models' => [],

    // Explicitly exclude models from logging.
    'exclude_models' => [
        Modules\AuditLog\Models\AuditLog::class,
    ],

    // Optional per-model event rules.
    // Example:
    // 'model_event_rules' => [
    //     App\Models\User::class => ['updated', 'deleted'],
    //     Modules\Donation\Models\DonationCampaign::class => ['created', 'updated'],
    //     '*' => ['created', 'updated', 'deleted', 'restored'], // default fallback
    // ],
    'model_event_rules' => [],

    // Common attributes to exclude from audit payload.
    'ignored_attributes' => [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ],

    // Exact attribute names that should be masked in logs.
    'masked_attributes' => [
        'password',
        'password_confirmation',
        'remember_token',
        'token',
        'api_key',
        'secret',
        'client_secret',
        'access_token',
        'refresh_token',
    ],

    // Regex patterns for sensitive keys (applied case-insensitively).
    'masked_attribute_patterns' => [
        '/password/i',
        '/token/i',
        '/secret/i',
        '/api[_-]?key/i',
        '/authorization/i',
        '/credential/i',
    ],

    'mask_placeholder' => '******',

    // Skip generating logs when running console commands (seeders, artisan tasks).
    'log_in_console' => env('AUDITLOG_LOG_IN_CONSOLE', false),

    // Trim long attribute values to keep payload manageable.
    'max_attribute_length' => 2000,
];

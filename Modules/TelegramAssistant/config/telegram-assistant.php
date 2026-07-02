<?php

return [
    'telegram' => [
        'bot_token'      => env('TELEGRAM_BOT_TOKEN'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    ],

    'openrouter' => [
        'api_key'  => env('OPENROUTER_API_KEY'),
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'model'    => env('OPENROUTER_MODEL', 'anthropic/claude-3.5-sonnet'),
    ],

    // Batas iterasi tool-call dalam satu percakapan, mencegah infinite loop.
    'max_tool_iterations' => 4,
];
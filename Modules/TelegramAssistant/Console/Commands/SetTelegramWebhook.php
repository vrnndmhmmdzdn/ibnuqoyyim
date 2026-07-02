<?php

namespace Modules\TelegramAssistant\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';

    protected $description = 'Daftarkan URL webhook Telegram bot ke Telegram API';

    public function handle(): int
    {
        $token = config('telegram-assistant.telegram.bot_token');
        $secret = config('telegram-assistant.telegram.webhook_secret');
        $url = route('telegram.webhook');

        if (blank($token)) {
            $this->error('TELEGRAM_BOT_TOKEN belum dikonfigurasi di .env');

            return self::FAILURE;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", [
            'url' => $url,
            'secret_token' => $secret,
        ]);

        if (! $response->successful() || ! $response->json('ok')) {
            $this->error('Gagal mendaftarkan webhook: ' . $response->body());

            return self::FAILURE;
        }

        $this->info("Webhook berhasil didaftarkan ke: {$url}");

        return self::SUCCESS;
    }
}
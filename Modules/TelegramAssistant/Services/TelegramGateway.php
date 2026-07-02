<?php

namespace Modules\TelegramAssistant\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramGateway
{
    public function sendMessage(string $chatId, string $text): bool
    {
        $token = config('telegram-assistant.telegram.bot_token');

        if (blank($token)) {
            Log::warning('TELEGRAM_BOT_TOKEN belum dikonfigurasi.');

            return false;
        }

        $response = Http::timeout(15)->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);

        if (! $response->successful()) {
            Log::warning('Gagal mengirim pesan Telegram.', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}
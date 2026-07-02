<?php

namespace Modules\TelegramAssistant\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenRouterService
{
    /**
     * Kirim satu putaran chat completion ke OpenRouter, dengan dukungan
     * function calling (tools). Mengembalikan message object mentah dari API
     * supaya orchestrator bisa cek apakah ada tool_calls atau jawaban teks biasa.
     *
     * @param  array<int, array<string, mixed>>  $messages
     * @param  array<int, array<string, mixed>>  $tools
     * @return array<string, mixed>
     */
    public function chat(array $messages, array $tools = []): array
    {
        $apiKey = config('telegram-assistant.openrouter.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENROUTER_API_KEY belum dikonfigurasi.');
        }

        $payload = [
            'model' => config('telegram-assistant.openrouter.model'),
            'messages' => $messages,
        ];

        if (filled($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post(rtrim(config('telegram-assistant.openrouter.base_url'), '/') . '/chat/completions', $payload);

        if (! $response->successful()) {
            Log::warning('OpenRouter request gagal.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Gagal menghubungi OpenRouter: ' . $response->status());
        }

        $message = $response->json('choices.0.message');

        if (blank($message)) {
            throw new RuntimeException('Respons OpenRouter tidak berisi message.');
        }

        return $message;
    }
}
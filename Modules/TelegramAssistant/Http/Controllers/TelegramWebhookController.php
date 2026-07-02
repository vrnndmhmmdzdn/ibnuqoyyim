<?php

namespace Modules\TelegramAssistant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\TelegramAssistant\Models\TelegramUser;
use Modules\TelegramAssistant\Services\AssistantOrchestrator;
use Modules\TelegramAssistant\Services\TelegramGateway;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, AssistantOrchestrator $orchestrator, TelegramGateway $telegram): Response
    {
        if (! $this->hasValidSecret($request)) {
            return response()->noContent(403);
        }

        $chatId = $request->input('message.chat.id');
        $text = trim((string) $request->input('message.text', ''));

        if (blank($chatId) || blank($text)) {
            return response()->noContent();
        }

        $telegramUser = TelegramUser::query()
            ->where('chat_id', (string) $chatId)
            ->where('is_active', true)
            ->first();

        if (! $telegramUser) {
            $telegram->sendMessage((string) $chatId, 'Maaf, nomor Anda belum terdaftar untuk menggunakan asisten ini. Hubungi admin sekolah.');

            return response()->noContent();
        }

        $telegramUser->update(['last_interaction_at' => now()]);

        $reply = $orchestrator->handle($telegramUser, $text);

        $telegram->sendMessage($telegramUser->chat_id, $reply);

        return response()->noContent();
    }

    protected function hasValidSecret(Request $request): bool
    {
        $expected = config('telegram-assistant.telegram.webhook_secret');

        if (blank($expected)) {
            return true;
        }

        return hash_equals($expected, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'));
    }
}
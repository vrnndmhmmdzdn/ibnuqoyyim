<?php

namespace Modules\TelegramAssistant\Services;

use Modules\TelegramAssistant\Models\AssistantAuditLog;
use Modules\TelegramAssistant\Models\TelegramUser;
use Modules\TelegramAssistant\Support\ToolAccessDeniedException;
use Modules\TelegramAssistant\Support\ToolRegistry;
use Throwable;

class AssistantOrchestrator
{
    public function __construct(
        protected OpenRouterService $openRouter,
        protected ToolRegistry $tools,
        protected TelegramGateway $telegram,
    ) {}

    public function handle(TelegramUser $requester, string $userMessage): string
    {
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt($requester)],
            ['role' => 'user', 'content' => $userMessage],
        ];

        $maxIterations = (int) config('telegram-assistant.max_tool_iterations', 4);

        for ($i = 0; $i < $maxIterations; $i++) {
            try {
                $message = $this->openRouter->chat($messages, $this->tools->definitions());
            } catch (Throwable $e) {
                $this->log($requester, $userMessage, null, null, null, 'error', $e->getMessage());

                return 'Maaf, terjadi kendala saat menghubungi asisten. Silakan coba lagi.';
            }

            $toolCalls = $message['tool_calls'] ?? null;

            if (blank($toolCalls)) {
                $answer = $message['content'] ?? 'Maaf, saya tidak bisa memproses permintaan itu.';

                $this->log($requester, $userMessage, null, null, $answer, 'answered');

                return $answer;
            }

            $messages[] = $message;

            foreach ($toolCalls as $toolCall) {
                $toolName = $toolCall['function']['name'] ?? '';
                $arguments = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?: [];

                [$resultContent, $status, $errorMessage] = $this->runTool($requester, $userMessage, $toolName, $arguments);

                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $toolCall['id'] ?? '',
                    'content' => json_encode($resultContent),
                ];
            }
        }

        return 'Maaf, permintaan ini terlalu kompleks untuk diproses saat ini. Coba pecah jadi pertanyaan yang lebih spesifik.';
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array{0: mixed, 1: string, 2: ?string}
     */
    protected function runTool(TelegramUser $requester, string $userMessage, string $toolName, array $arguments): array
    {
        try {
            $result = $this->tools->execute($toolName, $arguments, $requester);

            $this->log($requester, $userMessage, $toolName, $arguments, $result, 'tool_executed');

            return [$result, 'tool_executed', null];
        } catch (ToolAccessDeniedException $e) {
            $this->log($requester, $userMessage, $toolName, $arguments, null, 'denied', $e->getMessage());

            return [['error' => $e->getMessage()], 'denied', $e->getMessage()];
        } catch (Throwable $e) {
            $this->log($requester, $userMessage, $toolName, $arguments, null, 'error', $e->getMessage());

            return [['error' => 'Terjadi kesalahan saat menjalankan tool.'], 'error', $e->getMessage()];
        }
    }

    protected function systemPrompt(TelegramUser $requester): string
    {
        return "Anda adalah asisten dashboard sekolah. Jawab dengan singkat, jelas, dan dalam Bahasa Indonesia. "
            . "Gunakan tool yang tersedia untuk mengambil data nyata, jangan mengarang jawaban. "
            . "Peran user saat ini: {$requester->role}.";
    }

    /**
     * @param  array<string, mixed>|null  $arguments
     */
    protected function log(
        TelegramUser $requester,
        string $userMessage,
        ?string $toolName,
        ?array $arguments,
        mixed $result,
        string $status,
        ?string $errorMessage = null,
    ): void {
        AssistantAuditLog::create([
            'telegram_user_id' => $requester->id,
            'channel' => 'telegram',
            'user_message' => $userMessage,
            'tool_name' => $toolName,
            'arguments' => $arguments,
            'result' => is_array($result) ? $result : ['value' => $result],
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }
}
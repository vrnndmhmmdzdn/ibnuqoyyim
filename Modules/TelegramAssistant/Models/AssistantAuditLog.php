<?php

namespace Modules\TelegramAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssistantAuditLog extends Model
{
    protected $table = 'assistant_audit_logs';

    protected $fillable = [
        'telegram_user_id',
        'channel',
        'user_message',
        'tool_name',
        'arguments',
        'result',
        'status',
        'error_message',
    ];

    protected $casts = [
        'arguments' => 'array',
        'result' => 'array',
    ];

    const STATUS = [
        'answered'      => 'Dijawab Langsung',
        'tool_executed' => 'Tool Dieksekusi',
        'denied'        => 'Ditolak',
        'error'         => 'Error',
    ];

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }
}
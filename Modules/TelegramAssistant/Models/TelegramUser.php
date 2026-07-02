<?php

namespace Modules\TelegramAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    protected $table = 'telegram_users';

    protected $fillable = [
        'chat_id',
        'name',
        'user_id',
        'role',
        'is_active',
        'last_interaction_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_interaction_at' => 'datetime',
    ];

    const ROLES = [
        'admin' => 'Admin',
        'guru'  => 'Guru',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AssistantAuditLog::class);
    }

    /**
     * Daftar nama tool yang boleh dipanggil oleh user ini.
     * '*' berarti semua tool diizinkan.
     *
     * @return array<int, string>
     */
    public function allowedTools(): array
    {
        return match ($this->role) {
            'admin' => ['*'],
            'guru'  => ['cek_jurnal_belum_diisi', 'ringkasan_hafalan_kelas'],
            default => [],
        };
    }

    public function isAllowedToUse(string $toolName): bool
    {
        $allowed = $this->allowedTools();

        return in_array('*', $allowed, true) || in_array($toolName, $allowed, true);
    }
}
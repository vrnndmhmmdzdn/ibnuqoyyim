<?php

namespace Modules\AuditLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event',
        'auditable_type',
        'auditable_id',
        'actor_type',
        'actor_id',
        'ip_address',
        'method',
        'url',
        'user_agent',
        'old_values',
        'new_values',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function getAuditableLabelAttribute(): string
    {
        return class_basename($this->auditable_type) . '#' . $this->auditable_id;
    }

    public function getDiffHtmlAttribute(): string
    {
        $oldValues = is_array($this->old_values) ? $this->old_values : [];
        $newValues = is_array($this->new_values) ? $this->new_values : [];

        $keys = array_values(array_unique(array_merge(array_keys($oldValues), array_keys($newValues))));
        sort($keys);

        if (empty($keys)) {
            return '-';
        }

        $rows = '';
        foreach ($keys as $key) {
            $old = $oldValues[$key] ?? null;
            $new = $newValues[$key] ?? null;

            if ($old === $new) {
                continue;
            }

            $rows .= '<tr>';
            $rows .= '<td style="padding:8px;border:1px solid #e2e8f0;font-weight:600;">' . e((string) $key) . '</td>';
            $rows .= '<td style="padding:8px;border:1px solid #e2e8f0;">' . $this->formatDiffValue($old) . '</td>';
            $rows .= '<td style="padding:8px;border:1px solid #e2e8f0;">' . $this->formatDiffValue($new) . '</td>';
            $rows .= '</tr>';
        }

        if ($rows === '') {
            return '-';
        }

        return '<table style="width:100%;border-collapse:collapse;font-size:12px;">'
            . '<thead><tr>'
            . '<th style="text-align:left;padding:8px;border:1px solid #e2e8f0;">Field</th>'
            . '<th style="text-align:left;padding:8px;border:1px solid #e2e8f0;">Before</th>'
            . '<th style="text-align:left;padding:8px;border:1px solid #e2e8f0;">After</th>'
            . '</tr></thead>'
            . '<tbody>' . $rows . '</tbody>'
            . '</table>';
    }

    protected function formatDiffValue(mixed $value): string
    {
        if ($value === null) {
            return '<span style="color:#94a3b8;">null</span>';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return '<pre style="margin:0;white-space:pre-wrap;">' . e(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . '</pre>';
        }

        return e((string) $value);
    }

    public static function tableSizeBytes(): ?int
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $table = (new static())->getTable();

        try {
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                $row = $connection->selectOne(
                    'SELECT (data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?',
                    [$table]
                );

                return isset($row->size) ? (int) $row->size : null;
            }

            if ($driver === 'pgsql') {
                $row = $connection->selectOne('SELECT pg_total_relation_size(?) AS size', [$table]);

                return isset($row->size) ? (int) $row->size : null;
            }

            if ($driver === 'sqlite') {
                $database = config('database.connections.sqlite.database');
                if (!$database || $database === ':memory:') {
                    return null;
                }

                return is_file($database) ? (int) filesize($database) : null;
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    public static function humanTableSize(): string
    {
        $bytes = static::tableSizeBytes();
        if ($bytes === null) {
            return 'N/A';
        }

        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $size = $bytes / 1024;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return number_format($size, 2) . ' ' . $units[$unitIndex];
    }
}

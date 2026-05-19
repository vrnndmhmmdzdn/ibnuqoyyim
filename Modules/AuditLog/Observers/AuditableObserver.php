<?php

namespace Modules\AuditLog\Observers;

use Illuminate\Database\Eloquent\Model;
use Modules\AuditLog\Models\AuditLog;

class AuditableObserver
{
    public function created(Model $model): void
    {
        $this->writeLog('created', $model, [], $this->sanitizeAttributes($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];
        $newValues = [];

        foreach (array_keys($changes) as $attribute) {
            $oldValues[$attribute] = $model->getOriginal($attribute);
            $newValues[$attribute] = $model->getAttribute($attribute);
        }

        $this->writeLog(
            'updated',
            $model,
            $this->sanitizeAttributes($oldValues),
            $this->sanitizeAttributes($newValues)
        );
    }

    public function deleted(Model $model): void
    {
        $this->writeLog('deleted', $model, $this->sanitizeAttributes($model->getOriginal()), []);
    }

    public function restored(Model $model): void
    {
        $this->writeLog('restored', $model, [], $this->sanitizeAttributes($model->getAttributes()));
    }

    protected function writeLog(string $event, Model $model, array $oldValues, array $newValues): void
    {
        if (!$this->shouldLog($model, $event)) {
            return;
        }

        $user = auth()->user();
        $request = app()->bound('request') ? request() : null;

        AuditLog::query()->create([
            'event' => $event,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => (string) $model->getKey(),
            'actor_type' => $user ? $user::class : null,
            'actor_id' => $user?->getAuthIdentifier(),
            'ip_address' => $request?->ip(),
            'method' => $request?->method(),
            'url' => $this->sanitizeUrl($request?->fullUrl()),
            'user_agent' => $request?->userAgent(),
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'created_at' => now(),
        ]);
    }

    protected function shouldLog(Model $model, string $event): bool
    {
        if ($model instanceof AuditLog) {
            return false;
        }

        if (app()->runningInConsole() && !config('auditlog.log_in_console', false)) {
            return false;
        }

        if (config('auditlog.enabled', true) !== true) {
            return false;
        }

        return $this->isEventAllowedForModel($model, $event);
    }

    protected function isEventAllowedForModel(Model $model, string $event): bool
    {
        $rules = config('auditlog.model_event_rules', []);
        if (!is_array($rules) || empty($rules)) {
            return true;
        }

        $event = strtolower($event);
        $candidates = [
            $model::class,
            $model->getMorphClass(),
            class_basename($model::class),
            '*',
        ];

        foreach ($candidates as $key) {
            if (!array_key_exists($key, $rules)) {
                continue;
            }

            $allowedEvents = $rules[$key];
            if (!is_array($allowedEvents)) {
                return true;
            }

            $normalized = array_map(static fn ($item) => strtolower((string) $item), $allowedEvents);

            return in_array($event, $normalized, true);
        }

        return true;
    }

    protected function sanitizeAttributes(array $attributes): array
    {
        $ignored = config('auditlog.ignored_attributes', []);

        $result = [];
        foreach ($attributes as $key => $value) {
            if (in_array((string) $key, $ignored, true)) {
                continue;
            }

            $result[$key] = $this->sanitizeValue((string) $key, $value);
        }

        return $result;
    }

    protected function sanitizeValue(string $key, mixed $value): mixed
    {
        if ($this->shouldMask($key)) {
            return (string) config('auditlog.mask_placeholder', '******');
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $nestedKey => $nestedValue) {
                $result[$nestedKey] = $this->sanitizeValue((string) $nestedKey, $nestedValue);
            }

            return $result;
        }

        if (is_object($value)) {
            $decoded = json_decode(json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);
            if (is_array($decoded)) {
                return $this->sanitizeValue($key, $decoded);
            }

            return $this->trim((string) $value, (int) config('auditlog.max_attribute_length', 2000));
        }

        if (is_bool($value) || is_int($value) || is_float($value) || $value === null) {
            return $value;
        }

        return $this->trim((string) $value, (int) config('auditlog.max_attribute_length', 2000));
    }

    protected function shouldMask(string $key): bool
    {
        $normalizedKey = strtolower(trim($key));

        foreach ((array) config('auditlog.masked_attributes', []) as $maskedKey) {
            if ($normalizedKey === strtolower((string) $maskedKey)) {
                return true;
            }
        }

        foreach ((array) config('auditlog.masked_attribute_patterns', []) as $pattern) {
            if (!is_string($pattern) || $pattern === '') {
                continue;
            }

            $matched = @preg_match($pattern, $key);
            if ($matched === 1) {
                return true;
            }
        }

        return false;
    }

    protected function trim(string $value, int $maxLength): string
    {
        if ($maxLength <= 0 || strlen($value) <= $maxLength) {
            return $value;
        }

        return substr($value, 0, $maxLength) . '...';
    }

    protected function sanitizeUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return $this->trim($url, (int) config('auditlog.max_attribute_length', 2000));
        }

        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            $query = $this->sanitizeQueryParams($query);
        }

        $sanitized = '';
        if (isset($parts['scheme'])) {
            $sanitized .= $parts['scheme'] . '://';
        }

        if (isset($parts['user'])) {
            $sanitized .= $parts['user'];
            if (isset($parts['pass'])) {
                $sanitized .= ':' . $parts['pass'];
            }
            $sanitized .= '@';
        }

        $sanitized .= $parts['host'] ?? '';

        if (isset($parts['port'])) {
            $sanitized .= ':' . $parts['port'];
        }

        $sanitized .= $parts['path'] ?? '';

        if (!empty($query)) {
            $sanitized .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        if (isset($parts['fragment'])) {
            $sanitized .= '#' . $parts['fragment'];
        }

        return $this->trim($sanitized !== '' ? $sanitized : $url, (int) config('auditlog.max_attribute_length', 2000));
    }

    protected function sanitizeQueryParams(array $params): array
    {
        $sanitized = [];

        foreach ($params as $key => $value) {
            $keyString = (string) $key;

            if ($this->shouldMask($keyString)) {
                $sanitized[$key] = (string) config('auditlog.mask_placeholder', '******');
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeQueryParams($value);
                continue;
            }

            if (is_scalar($value) || $value === null) {
                $sanitized[$key] = $this->trim((string) $value, (int) config('auditlog.max_attribute_length', 2000));
                continue;
            }

            $sanitized[$key] = $this->trim((string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), (int) config('auditlog.max_attribute_length', 2000));
        }

        return $sanitized;
    }
}

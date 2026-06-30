<?php

namespace Modules\DashboardBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardChart extends Model
{
    protected $table = 'dashboard_builder_charts';

    protected $fillable = [
        'dashboard_id',
        'title',
        'sort_order',
        'config_json',
    ];

    protected function casts(): array
    {
        return [
            'config_json' => 'array',
        ];
    }

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class, 'dashboard_id');
    }

    public function getConfigValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->config_json ?? [], $key, $default);
    }
}

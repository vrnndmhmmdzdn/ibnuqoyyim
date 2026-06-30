<?php

namespace Modules\DashboardBuilder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dashboard extends Model
{
    protected $table = 'dashboard_builder_dashboards';

    protected $fillable = [
        'name',
        'slug',
        'public_uuid',
        'description',
        'is_active',
        'is_published',
        'published_requires_login',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            'published_requires_login' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function charts(): HasMany
    {
        return $this->hasMany(DashboardChart::class, 'dashboard_id')->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getPublishedUrl(): ?string
    {
        if (blank($this->public_uuid) || (! $this->is_published)) {
            return null;
        }

        return route('dashboard-builder.published', ['uuid' => $this->public_uuid]);
    }

    public function getPreviewUrl(): string
    {
        return route('dashboard-builder.preview', ['dashboard' => $this]);
    }
}

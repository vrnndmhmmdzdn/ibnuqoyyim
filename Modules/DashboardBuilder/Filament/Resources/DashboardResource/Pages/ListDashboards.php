<?php

namespace Modules\DashboardBuilder\Filament\Resources\DashboardResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use Modules\DashboardBuilder\Filament\Resources\DashboardResource;
use Modules\DashboardBuilder\Models\Dashboard;

class ListDashboards extends ListRecords
{
    protected static string $resource = DashboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Dashboard')
                ->icon('heroicon-o-plus')
                ->modalHeading('Create Dashboard')
                ->modalSubmitActionLabel('Create and Build')
                ->createAnother(false)
                ->mutateDataUsing(fn (array $data): array => [
                    ...$data,
                    'name' => trim((string) ($data['name'] ?? '')),
                    'slug' => $this->generateUniqueSlug((string) ($data['name'] ?? '')),
                ])
                ->successNotificationTitle('Dashboard created successfully')
                ->successRedirectUrl(fn (Dashboard $record): string => static::getResource()::getUrl('edit', ['record' => $record])),
        ];
    }

    protected function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'dashboard';
        $slug = $base;
        $suffix = 2;

        while (Dashboard::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}

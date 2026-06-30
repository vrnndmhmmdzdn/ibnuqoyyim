<?php

namespace Modules\DashboardBuilder\Filament\Resources\DashboardResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Modules\DashboardBuilder\Filament\Resources\DashboardResource;
use Modules\DashboardBuilder\Models\Dashboard;
use Modules\DashboardBuilder\Services\DashboardRenderService;

class ViewDashboard extends Page
{
    protected static string $resource = DashboardResource::class;

    protected string $view = 'dashboard-builder::filament.resources.dashboard-resource.pages.dashboard-view';

    public Dashboard $record;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $renderedCharts = [];

    public function mount(Dashboard $record): void
    {
        $this->record = $record->load('charts');
        $this->renderedCharts = app(DashboardRenderService::class)->build($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit Layout')
                ->icon('heroicon-o-pencil-square')
                ->url(static::getResource()::getUrl('edit', ['record' => $this->record])),
        ];
    }

    public function getTitle(): string
    {
        return $this->record->name ?: 'Dashboard';
    }

    public function getChartWidthClass(array $config): string
    {
        return match ($config['width'] ?? 'half') {
            'third' => 'md:col-span-4',
            'two-thirds' => 'md:col-span-8',
            'full' => 'md:col-span-12',
            default => 'md:col-span-6',
        };
    }
}

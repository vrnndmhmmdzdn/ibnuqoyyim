<?php

namespace Modules\DashboardBuilder\Livewire;

use Filament\Facades\Filament;
use Livewire\Component;
use Modules\DashboardBuilder\Models\Dashboard;
use Modules\DashboardBuilder\Services\DashboardRenderService;

class PublishedDashboardPage extends Component
{
    public Dashboard $dashboard;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $renderedCharts = [];

    public function mount(string $uuid): void
    {
        $this->dashboard = Dashboard::query()
            ->published()
            ->where('public_uuid', $uuid)
            ->with('charts')
            ->firstOrFail();

        if ($this->dashboard->published_requires_login && (! Filament::getPanel('admin')->auth()->check())) {
            $this->redirect(route('dashboard-builder.published.access', ['uuid' => $uuid]), navigate: false);

            return;
        }

        $this->renderedCharts = app(DashboardRenderService::class)->build($this->dashboard);
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

    public function render()
    {
        return view('dashboard-builder::livewire.dashboard-display-page', [
            'dashboard' => $this->dashboard,
            'pageTitle' => $this->dashboard->name,
            'showPreviewBadge' => false,
            'renderedCharts' => $this->renderedCharts,
        ])->layout('dashboard-builder::layouts.dashboard-display', [
            'pageTitle' => $this->dashboard->name,
        ]);
    }
}

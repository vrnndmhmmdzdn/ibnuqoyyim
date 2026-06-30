<?php

namespace Modules\DashboardBuilder\Services;

use Modules\DashboardBuilder\Models\Dashboard;

class DashboardRenderService
{
    public function __construct(
        protected DashboardChartDataService $chartDataService,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function build(Dashboard $dashboard): array
    {
        return $dashboard->charts
            ->sortBy('sort_order')
            ->values()
            ->map(function ($chart): array {
                return [
                    'id' => $chart->id,
                    'title' => $chart->title,
                    'sort_order' => $chart->sort_order,
                    'config' => $chart->config_json ?? [],
                    'payload' => $this->chartDataService->build($chart->config_json ?? []),
                ];
            })
            ->all();
    }
}

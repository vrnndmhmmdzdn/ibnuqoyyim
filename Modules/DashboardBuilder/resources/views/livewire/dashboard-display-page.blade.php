<div class="min-h-screen">
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white px-6 py-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($showPreviewBadge)
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">
                                Preview
                            </span>
                        @endif

                        @if ($dashboard->is_published)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                                Published
                            </span>
                        @endif

                        @if ($dashboard->published_requires_login)
                            <span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700">
                                Login Required
                            </span>
                        @endif
                    </div>

                    <div>
                        <h1 class="text-3xl font-semibold tracking-tight text-slate-950">{{ $dashboard->name }}</h1>

                        @if (filled($dashboard->description))
                            <p class="mt-2 max-w-3xl text-sm text-slate-500">{{ $dashboard->description }}</p>
                        @endif
                    </div>
                </div>

                @if ($showPreviewBadge)
                    <a
                        href="{{ \Modules\DashboardBuilder\Filament\Resources\DashboardResource::getUrl('edit', ['record' => $dashboard]) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                    >
                        Back to Builder
                    </a>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(245,158,11,0.08),_transparent_35%)] p-4 shadow-sm">
            @if ($renderedCharts === [])
                <div class="flex min-h-80 items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white/60 p-8 text-center text-sm text-slate-500">
                    This dashboard does not have any charts yet.
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    @foreach ($renderedCharts as $chart)
                        <div wire:key="standalone-dashboard-chart-{{ $chart['id'] }}" class="{{ $this->getChartWidthClass($chart['config']) }}">
                            @include('dashboard-builder::livewire.partials.chart-card', [
                                'title' => $chart['title'],
                                'payload' => $chart['payload'],
                                'ratio' => $chart['config']['ratio'] ?? '16/9',
                                'chartKey' => $chart['id'] . '-standalone',
                            ])
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

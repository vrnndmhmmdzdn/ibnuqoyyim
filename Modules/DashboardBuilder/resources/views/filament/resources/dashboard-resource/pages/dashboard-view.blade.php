<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="flex flex-col gap-2">
                <h2 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ $record->name }}</h2>

                @if (filled($record->description))
                    <p class="max-w-3xl text-sm text-gray-500 dark:text-gray-400">{{ $record->description }}</p>
                @endif
            </div>
        </x-filament::section>

        <div class="rounded-2xl border border-white/70 bg-[radial-gradient(circle_at_top_left,_rgba(245,158,11,0.08),_transparent_35%)] p-4 shadow-sm dark:border-white/10 dark:bg-[radial-gradient(circle_at_top_left,_rgba(245,158,11,0.14),_transparent_35%)]">
            @if ($renderedCharts === [])
                <div class="flex min-h-80 items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white/60 p-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-950/40 dark:text-gray-400">
                    This dashboard does not have any charts yet.
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    @foreach ($renderedCharts as $chart)
                        <div wire:key="dashboard-view-chart-{{ $chart['id'] }}" class="{{ $this->getChartWidthClass($chart['config']) }}">
                            @include('dashboard-builder::filament.resources.dashboard-resource.partials.chart-card', [
                                'title' => $chart['title'],
                                'payload' => $chart['payload'],
                                'ratio' => $chart['config']['ratio'] ?? '16/9',
                                'selected' => false,
                                'editable' => false,
                                'chartKey' => $chart['id'] . '-view',
                            ])
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>

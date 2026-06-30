<x-filament-panels::page>
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_24rem]">
        <div class="space-y-6">
            <x-filament::section description="This builder uses the same layout as the final dashboard, so the preview stays close to the final result.">
                <x-slot name="heading">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="fi-section-header-heading">Canvas Dashboard</div>
                        </div>

                        <x-filament::button color="gray" wire:click="addChart" icon="heroicon-o-plus">
                            Add Chart
                        </x-filament::button>
                    </div>
                </x-slot>

                <div class="rounded-2xl border border-dashed border-gray-300 bg-[radial-gradient(circle_at_top_left,_rgba(245,158,11,0.08),_transparent_35%),linear-gradient(to_right,rgba(148,163,184,0.08)_1px,transparent_1px),linear-gradient(to_bottom,rgba(148,163,184,0.08)_1px,transparent_1px)] bg-[size:auto,32px_32px,32px_32px] p-4 dark:border-gray-700 dark:bg-[radial-gradient(circle_at_top_left,_rgba(245,158,11,0.14),_transparent_35%),linear-gradient(to_right,rgba(71,85,105,0.16)_1px,transparent_1px),linear-gradient(to_bottom,rgba(71,85,105,0.16)_1px,transparent_1px)]">
                    @if ($charts === [])
                        <div class="flex min-h-80 items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-white/60 p-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-950/40 dark:text-gray-400">
                            Add the first chart to start building the dashboard.
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
                            @foreach ($charts as $index => $chart)
                                <div
                                    wire:key="builder-chart-{{ $chart['id'] ?? 'new-' . $index }}"
                                    class="{{ $this->getChartWidthClass($chart['config']) }}"
                                >
                                    <div class="space-y-3 rounded-2xl border border-white/60 bg-white/80 p-3 shadow-sm backdrop-blur dark:border-white/10 dark:bg-gray-950/60">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <button
                                                type="button"
                                                wire:click="selectChart({{ $index }})"
                                                class="inline-flex items-center gap-2 text-left text-sm font-semibold text-gray-950 transition hover:text-primary-600 dark:text-white"
                                            >
                                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-300">
                                                    {{ $index + 1 }}
                                                </span>
                                                {{ $chart['title'] }}
                                            </button>

                                            <div class="flex flex-wrap items-center gap-2">
                                                <button type="button" wire:click="moveChartUp({{ $index }})" class="rounded-lg border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-900">Up</button>
                                                <button type="button" wire:click="moveChartDown({{ $index }})" class="rounded-lg border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-900">Down</button>
                                                <button type="button" wire:click="duplicateChart({{ $index }})" class="rounded-lg border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-900">Duplicate</button>
                                                <button type="button" wire:click="removeChart({{ $index }})" class="rounded-lg border border-danger-200 px-2 py-1 text-xs text-danger-600 hover:bg-danger-50 dark:border-danger-500/30 dark:text-danger-300 dark:hover:bg-danger-500/10">Delete</button>
                                            </div>
                                        </div>

                                        @include('dashboard-builder::filament.resources.dashboard-resource.partials.chart-card', [
                                            'title' => $chart['title'],
                                            'payload' => $this->getChartPreview($index),
                                            'ratio' => $chart['config']['ratio'] ?? '16/9',
                                            'selected' => $selectedChartIndex === $index,
                                            'editable' => true,
                                            'chartKey' => ($chart['id'] ?? 'new-' . $index) . '-builder',
                                        ])
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-filament::section>
        </div>

        <div class="space-y-6 self-start xl:sticky xl:top-20 xl:z-10">
            <x-filament::section heading="Chart Settings" description="Select a chart on the canvas to configure its data source and appearance.">
                @if ($selectedChartIndex === null || ! isset($charts[$selectedChartIndex]))
                    <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        No chart is currently selected.
                    </div>
                @else
                    @php
                        $selectedChart = $charts[$selectedChartIndex];
                        $table = $selectedChart['config']['table'] ?? null;
                        $isCount = ($selectedChart['config']['aggregate'] ?? 'count') === 'count';
                    @endphp

                    <div class="space-y-5" wire:key="chart-settings-{{ $selectedChartIndex }}">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Chart Title</label>
                            <x-filament::input.wrapper>
                                <x-filament::input
                                    type="text"
                                    wire:model.live="charts.{{ $selectedChartIndex }}.title"
                                />
                            </x-filament::input.wrapper>
                            @error("charts.$selectedChartIndex.title")
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Chart Type</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="charts.{{ $selectedChartIndex }}.config.type">
                                        @foreach ($this->getChartTypeOptions() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Table / Entity</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="charts.{{ $selectedChartIndex }}.config.table">
                                        @foreach ($this->getTableOptions() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Grouping / Dimension</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="charts.{{ $selectedChartIndex }}.config.dimension">
                                        @foreach ($this->getDimensionOptions($table) as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Aggregation</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="charts.{{ $selectedChartIndex }}.config.aggregate">
                                        @foreach ($this->getAggregateOptions() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            <div class="{{ $isCount ? 'opacity-60' : '' }}">
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Value Column</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select
                                        wire:model.live="charts.{{ $selectedChartIndex }}.config.value_column"
                                        :disabled="$isCount"
                                    >
                                        <option value="">Auto / Not used</option>
                                        @foreach ($this->getNumericColumnOptions($table) as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Value Order</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="charts.{{ $selectedChartIndex }}.config.sort_direction">
                                        <option value="desc">Highest first</option>
                                        <option value="asc">Lowest first</option>
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Grid Width</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="charts.{{ $selectedChartIndex }}.config.width">
                                        @foreach ($this->getWidthOptions() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Chart Ratio</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input.select wire:model.live="charts.{{ $selectedChartIndex }}.config.ratio">
                                        @foreach ($this->getRatioOptions() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-950 dark:text-white">Data Limit</label>
                                <x-filament::input.wrapper>
                                    <x-filament::input
                                        type="number"
                                        min="1"
                                        max="20"
                                        wire:model.live="charts.{{ $selectedChartIndex }}.config.limit"
                                    />
                                </x-filament::input.wrapper>
                            </div>
                        </div>

                        <div class="rounded-xl bg-gray-50 p-4 text-xs text-gray-600 dark:bg-gray-900/70 dark:text-gray-300">
                            <div class="font-medium text-gray-800 dark:text-gray-100">JSON Preview</div>
                            <pre class="mt-3 overflow-x-auto whitespace-pre-wrap">{{ json_encode($selectedChart['config'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>

                        @if ($errors->any())
                            <div class="rounded-xl border border-danger-200 bg-danger-50 p-4 text-sm text-danger-700 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-200">
                                <div class="font-medium">Validation failed</div>
                                <ul class="mt-2 list-disc space-y-1 pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>

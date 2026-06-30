@props([
    'title' => 'Chart',
    'payload',
    'ratio' => '16/9',
    'selected' => false,
    'editable' => false,
    'chartKey' => null,
])

@php
    $aspectRatio = str_replace('/', ' / ', $ratio ?: '16/9');
    $sectionClass = \Illuminate\Support\Arr::toCssClasses([
        'h-full transition',
        'ring-2 ring-primary-500/70 shadow-lg' => $selected,
    ]);
    $chartElementId = 'dashboard-chart-' . md5(($chartKey ?? $title) . '-' . json_encode($payload));
@endphp

<x-filament::section
    :heading="$title"
    :class="$sectionClass"
>
    @if (filled($payload['error'] ?? null))
        <div class="rounded-xl border border-danger-200 bg-danger-50 p-4 text-sm text-danger-700 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-200">
            {{ $payload['error'] }}
        </div>
    @elseif (blank($payload['data']['labels'] ?? []))
        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
            Belum ada data yang bisa divisualisasikan untuk chart ini.
        </div>
    @else
        <div
            id="{{ $chartElementId }}"
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
            wire:ignore
            data-chart-type="{{ $payload['type'] }}"
            x-data="chart({
                cachedData: @js($payload['data']),
                options: @js($payload['options']),
                type: @js($payload['type']),
            })"
            class="fi-wi-chart-canvas-ctn rounded-xl bg-white/50 dark:bg-white/[0.02]"
            style="aspect-ratio: {{ $aspectRatio }};"
        >
            <canvas x-ref="canvas"></canvas>

            <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
            <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
            <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
            <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
        </div>
    @endif

    @if ($editable)
        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <span class="rounded-full bg-gray-100 px-2.5 py-1 dark:bg-gray-800">{{ strtoupper($payload['type']) }}</span>
            <span class="rounded-full bg-gray-100 px-2.5 py-1 dark:bg-gray-800">{{ $payload['meta']['table'] ?? '-' }}</span>
            <span class="rounded-full bg-gray-100 px-2.5 py-1 dark:bg-gray-800">{{ $payload['meta']['dimension_label'] ?? '-' }}</span>
            <span class="rounded-full bg-gray-100 px-2.5 py-1 dark:bg-gray-800">{{ strtoupper($payload['meta']['aggregate'] ?? '-') }}</span>
        </div>
    @endif
</x-filament::section>

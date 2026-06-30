@props([
    'title' => 'Chart',
    'payload',
    'ratio' => '16/9',
    'chartKey' => null,
])

@php
    $aspectRatio = str_replace('/', ' / ', $ratio ?: '16/9');
    $chartElementId = 'dashboard-standalone-chart-' . md5(($chartKey ?? $title) . '-' . json_encode($payload));
@endphp

<div class="h-full rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 px-5 py-4">
        <h2 class="text-base font-semibold text-slate-950">{{ $title }}</h2>
    </div>

    <div class="p-5">
        @if (filled($payload['error'] ?? null))
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                {{ $payload['error'] }}
            </div>
        @elseif (blank($payload['data']['labels'] ?? []))
            <div class="rounded-xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">
                No data is available to visualize for this chart.
            </div>
        @else
            <div
                id="{{ $chartElementId }}"
                x-data="dashboardStandaloneChart({
                    data: @js($payload['data']),
                    options: @js($payload['options']),
                    type: @js($payload['type']),
                })"
                class="rounded-xl bg-slate-50"
                style="aspect-ratio: {{ $aspectRatio }};"
            >
                <canvas x-ref="canvas"></canvas>

                <span x-ref="backgroundColorElement" class="hidden text-white"></span>
                <span x-ref="borderColorElement" class="hidden text-slate-300"></span>
                <span x-ref="gridColorElement" class="hidden text-slate-200"></span>
                <span x-ref="textColorElement" class="hidden text-slate-600"></span>
            </div>
        @endif
    </div>
</div>

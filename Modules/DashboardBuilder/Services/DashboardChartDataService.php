<?php

namespace Modules\DashboardBuilder\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class DashboardChartDataService
{
    public function __construct(
        protected DashboardTableSchemaService $schemaService,
    ) {}

    /**
     * @param  array<string, mixed>  $config
     * @return array{type: string, data: array<string, mixed>, options: array<string, mixed>, error: string|null, meta: array<string, mixed>}
     */
    public function build(array $config): array
    {
        $normalized = $this->normalizeConfig($config);

        if (! $normalized['table']) {
            return $this->emptyPayload($normalized, 'No table has been selected.');
        }

        try {
            $data = $this->buildQueryData($normalized);
        } catch (Throwable $exception) {
            return $this->emptyPayload($normalized, 'The chart query could not be executed: '.$exception->getMessage());
        }

        return [
            'type' => $normalized['type'],
            'data' => $data,
            'options' => $this->getOptions($normalized['type']),
            'error' => null,
            'meta' => [
                'table' => $normalized['table'],
                'aggregate' => $normalized['aggregate'],
                'dimension' => $normalized['dimension'],
                'dimension_label' => $this->schemaService->getDimensionDefinition($normalized['table'], $normalized['dimension'])['label'] ?? $normalized['dimension'],
                'label_column' => $normalized['dimension'],
                'value_column' => $normalized['value_column'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    public function normalizeConfig(array $config): array
    {
        $table = $config['table'] ?? $this->schemaService->getFirstAvailableTable();
        $columns = $table ? $this->schemaService->getColumns($table) : [];
        $dimensions = $table ? $this->schemaService->getDimensionDefinitions($table) : [];
        $aggregate = in_array($config['aggregate'] ?? 'count', ['count', 'sum', 'avg', 'min', 'max'], true)
            ? ($config['aggregate'] ?? 'count')
            : 'count';
        $dimension = $config['dimension'] ?? $config['group_by'] ?? $config['label_column'] ?? $this->schemaService->getFirstDimension($table ?? '');
        $valueColumn = $config['value_column'] ?? $this->schemaService->getFirstNumericColumn($columns);

        if (is_string($dimension) && ! str_contains($dimension, ':') && array_key_exists($dimension, $columns)) {
            $dimension = 'column:'.$dimension;
        }

        if (! array_key_exists((string) $dimension, $dimensions)) {
            $dimension = $this->schemaService->getFirstDimension($table ?? '');
        }

        if (($aggregate !== 'count') && ! array_key_exists((string) $valueColumn, $columns)) {
            $valueColumn = $this->schemaService->getFirstNumericColumn($columns);
        }

        return [
            'type' => in_array($config['type'] ?? 'bar', ['bar', 'line', 'pie', 'doughnut', 'polarArea'], true)
                ? ($config['type'] ?? 'bar')
                : 'bar',
            'table' => $table,
            'dimension' => $dimension,
            'group_by' => $dimension,
            'label_column' => $dimension,
            'value_column' => $valueColumn,
            'aggregate' => $aggregate,
            'width' => in_array((string) ($config['width'] ?? 'half'), ['third', 'half', 'two-thirds', 'full'], true)
                ? (string) ($config['width'] ?? 'half')
                : 'half',
            'ratio' => in_array((string) ($config['ratio'] ?? '16/9'), ['16/9', '4/3', '1/1', '21/9'], true)
                ? (string) ($config['ratio'] ?? '16/9')
                : '16/9',
            'limit' => min(max((int) ($config['limit'] ?? 10), 1), 20),
            'sort_direction' => in_array((string) ($config['sort_direction'] ?? 'desc'), ['asc', 'desc'], true)
                ? (string) ($config['sort_direction'] ?? 'desc')
                : 'desc',
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    protected function buildQueryData(array $config): array
    {
        if (! $config['dimension']) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $columns = $this->schemaService->getColumns($config['table']);
        $dimension = $this->schemaService->getDimensionDefinition($config['table'], $config['dimension']);

        if (! $dimension) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $query = DB::table($config['table']);

        $grammar = $query->getGrammar();
        $labelNotNullColumn = null;
        $groupExpression = null;

        if ($dimension['kind'] === 'relation') {
            $joinAlias = 'dashboard_dimension_relation';
            $query->leftJoin(
                $dimension['foreign_table'].' as '.$joinAlias,
                $config['table'].'.'.$dimension['local_column'],
                '=',
                $joinAlias.'.'.$dimension['foreign_column'],
            );

            $labelNotNullColumn = $joinAlias.'.'.$dimension['display_column'];
            $groupExpression = $grammar->wrap($labelNotNullColumn);
        } else {
            $labelNotNullColumn = $dimension['column'];
            $groupExpression = $grammar->wrap($labelNotNullColumn);
        }

        $query->selectRaw("{$groupExpression} as chart_label");

        $valueLabel = 'Total';

        if ($config['aggregate'] === 'count') {
            $query->selectRaw('COUNT(*) as chart_value');
        } else {
            $valueColumn = $config['value_column'];

            if (blank($valueColumn) || ! isset($columns[$valueColumn])) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }

            $wrappedValue = $grammar->wrap($valueColumn);
            $aggregateSql = Str::upper($config['aggregate']);

            $query->selectRaw("{$aggregateSql}({$wrappedValue}) as chart_value");
            $valueLabel = Str::upper($config['aggregate']).' '.($columns[$valueColumn]['label'] ?? Str::headline($valueColumn));
        }

        $rows = $this->applyGrouping($query, $labelNotNullColumn, $groupExpression, $config['sort_direction'], $config['limit'])
            ->get()
            ->map(fn (object $row): array => [
                'label' => (string) ($row->chart_label ?? 'Unknown'),
                'value' => (float) ($row->chart_value ?? 0),
            ]);

        return [
            'datasets' => [
                [
                    'label' => $valueLabel,
                    'data' => $rows->pluck('value')->all(),
                    'backgroundColor' => $this->getPalette($config['type'], $rows->count(), 0.72),
                    'borderColor' => $this->getPalette($config['type'], $rows->count(), 1),
                    'borderWidth' => 1.5,
                    'fill' => $config['type'] === 'line',
                    'tension' => $config['type'] === 'line' ? 0.32 : 0,
                ],
            ],
            'labels' => $rows->pluck('label')->all(),
        ];
    }

    protected function applyGrouping(Builder $query, string $labelNotNullColumn, string $groupExpression, string $sortDirection, int $limit): Builder
    {
        return $query
            ->whereNotNull($labelNotNullColumn)
            ->groupByRaw($groupExpression)
            ->orderBy('chart_value', $sortDirection)
            ->limit($limit);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(string $type): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => in_array($type, ['pie', 'doughnut', 'polarArea'], true),
                    'position' => 'bottom',
                ],
            ],
            'scales' => in_array($type, ['pie', 'doughnut', 'polarArea'], true)
                ? new \stdClass
                : [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{type: string, data: array<string, mixed>, options: array<string, mixed>, error: string|null, meta: array<string, mixed>}
     */
    protected function emptyPayload(array $config, ?string $error = null): array
    {
        return [
            'type' => $config['type'],
            'data' => [
                'datasets' => [],
                'labels' => [],
            ],
            'options' => $this->getOptions($config['type']),
            'error' => $error,
            'meta' => [
                'table' => $config['table'],
                'aggregate' => $config['aggregate'],
                'dimension' => $config['dimension'] ?? $config['label_column'] ?? null,
                'dimension_label' => $this->schemaService->getDimensionDefinition($config['table'] ?? '', $config['dimension'] ?? $config['label_column'] ?? null)['label'] ?? ($config['dimension'] ?? $config['label_column'] ?? null),
                'label_column' => $config['dimension'] ?? $config['label_column'] ?? null,
                'value_column' => $config['value_column'],
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getPalette(string $type, int $count, float $alpha): array
    {
        $base = [
            '59, 130, 246',
            '16, 185, 129',
            '245, 158, 11',
            '236, 72, 153',
            '139, 92, 246',
            '14, 165, 233',
            '249, 115, 22',
            '34, 197, 94',
            '244, 63, 94',
            '99, 102, 241',
        ];

        return collect(range(0, max($count - 1, 0)))
            ->map(fn (int $index): string => 'rgba('.$base[$index % count($base)].', '.$alpha.')')
            ->when(
                $type === 'line',
                fn ($colors) => $colors->map(fn (string $color, int $index): string => 'rgba('.$base[$index % count($base)].', 0.24)'),
            )
            ->all();
    }
}

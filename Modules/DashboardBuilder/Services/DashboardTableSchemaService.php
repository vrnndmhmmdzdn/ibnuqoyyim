<?php

namespace Modules\DashboardBuilder\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DashboardTableSchemaService
{
    /**
     * @var array<int, string>
     */
    protected array $ignoredTables = [
        'cache',
        'cache_locks',
        'failed_jobs',
        'job_batches',
        'jobs',
        'migrations',
        'password_reset_tokens',
        'sessions',
        'dashboard_builder_dashboards',
        'dashboard_builder_charts',
    ];

    /**
     * @return array<int, array{name: string, label: string}>
     */
    public function getTables(): array
    {
        $schemaListing = Schema::getConnection()->getSchemaBuilder()->getCurrentSchemaListing();

        return collect(Schema::getTables($schemaListing))
            ->pluck('name')
            ->filter(fn (?string $table): bool => filled($table))
            ->reject(fn (string $table): bool => in_array($table, $this->ignoredTables, true))
            ->sort()
            ->values()
            ->map(fn (string $table): array => [
                'name' => $table,
                'label' => Str::headline($table),
            ])
            ->all();
    }

    /**
     * @return array<string, array{name: string, label: string, type: string, type_name: string, category: string, nullable: bool}>
     */
    public function getColumns(string $table): array
    {
        if (! $this->tableExists($table)) {
            return [];
        }

        return collect(Schema::getColumns($table))
            ->mapWithKeys(function (array $column): array {
                $typeName = (string) ($column['type_name'] ?? $column['type'] ?? 'string');

                return [
                    $column['name'] => [
                        'name' => $column['name'],
                        'label' => Str::headline($column['name']),
                        'type' => (string) ($column['type'] ?? $typeName),
                        'type_name' => $typeName,
                        'category' => $this->resolveCategory($typeName),
                        'nullable' => (bool) ($column['nullable'] ?? false),
                    ],
                ];
            })
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function getGroupableColumnOptions(string $table): array
    {
        return collect($this->getColumns($table))
            ->reject(fn (array $column): bool => in_array($column['category'], ['json', 'binary'], true))
            ->mapWithKeys(fn (array $column): array => [$column['name'] => $column['label']])
            ->all();
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getDimensionDefinitions(string $table): array
    {
        if (! $this->tableExists($table)) {
            return [];
        }

        $tableColumns = $this->getColumns($table);

        $localDimensions = collect($tableColumns)
            ->reject(fn (array $column): bool => in_array($column['category'], ['json', 'binary'], true))
            ->mapWithKeys(fn (array $column): array => [
                'column:'.$column['name'] => [
                    'key' => 'column:'.$column['name'],
                    'kind' => 'column',
                    'label' => $column['label'],
                    'column' => $column['name'],
                ],
            ]);

        $relationDimensions = collect(Schema::getForeignKeys($table))
            ->filter(fn (array $foreignKey): bool => count($foreignKey['columns'] ?? []) === 1 && count($foreignKey['foreign_columns'] ?? []) === 1)
            ->map(function (array $foreignKey) use ($tableColumns): ?array {
                $foreignTable = $foreignKey['foreign_table'] ?? null;

                if (! $this->tableExists($foreignTable)) {
                    return null;
                }

                $displayColumn = $this->detectDisplayColumn($foreignTable);

                if (! $displayColumn) {
                    return null;
                }

                $localColumn = $foreignKey['columns'][0];
                $foreignColumn = $foreignKey['foreign_columns'][0];
                $foreignColumns = $this->getColumns($foreignTable);
                $key = 'relation:'.$localColumn.':'.$foreignTable.':'.$foreignColumn.':'.$displayColumn;

                return [
                    'key' => $key,
                    'kind' => 'relation',
                    'label' => ($tableColumns[$localColumn]['label'] ?? Str::headline($localColumn)).' > '.($foreignColumns[$displayColumn]['label'] ?? Str::headline($displayColumn)),
                    'local_column' => $localColumn,
                    'foreign_table' => $foreignTable,
                    'foreign_column' => $foreignColumn,
                    'display_column' => $displayColumn,
                ];
            })
            ->filter()
            ->mapWithKeys(fn (array $definition): array => [$definition['key'] => $definition]);

        return $localDimensions
            ->merge($relationDimensions)
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function getDimensionOptions(string $table): array
    {
        return collect($this->getDimensionDefinitions($table))
            ->mapWithKeys(fn (array $definition): array => [$definition['key'] => $definition['label']])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function getNumericColumnOptions(string $table): array
    {
        return collect($this->getColumns($table))
            ->filter(fn (array $column): bool => $column['category'] === 'numeric')
            ->mapWithKeys(fn (array $column): array => [$column['name'] => $column['label']])
            ->all();
    }

    public function tableExists(?string $table): bool
    {
        if (blank($table)) {
            return false;
        }

        return collect($this->getTables())->contains(fn (array $item): bool => $item['name'] === $table);
    }

    /**
     * @return array<string, string>
     */
    public function getTableOptions(): array
    {
        return collect($this->getTables())
            ->mapWithKeys(fn (array $table): array => [$table['name'] => $table['label']])
            ->all();
    }

    public function getFirstAvailableTable(): ?string
    {
        return $this->getTables()[0]['name'] ?? null;
    }

    public function getFirstDimension(string $table): ?string
    {
        return array_key_first($this->getDimensionDefinitions($table));
    }

    /**
     * @param  array<string, array{name: string, label: string, type: string, type_name: string, category: string, nullable: bool}>  $columns
     */
    public function getFirstGroupableColumn(array $columns): ?string
    {
        return collect($columns)
            ->first(fn (array $column): bool => ! in_array($column['category'], ['json', 'binary'], true))['name'] ?? null;
    }

    /**
     * @param  array<string, array{name: string, label: string, type: string, type_name: string, category: string, nullable: bool}>  $columns
     */
    public function getFirstNumericColumn(array $columns): ?string
    {
        return collect($columns)
            ->first(fn (array $column): bool => $column['category'] === 'numeric')['name'] ?? null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getDimensionDefinition(string $table, ?string $key): ?array
    {
        if (blank($table) || blank($key)) {
            return null;
        }

        return $this->getDimensionDefinitions($table)[$key] ?? null;
    }

    protected function resolveCategory(string $typeName): string
    {
        $typeName = Str::lower($typeName);

        return match (true) {
            Str::contains($typeName, ['int', 'decimal', 'float', 'double', 'real', 'numeric']) => 'numeric',
            Str::contains($typeName, ['date', 'time', 'year']) => 'date',
            Str::contains($typeName, ['json']) => 'json',
            Str::contains($typeName, ['binary', 'blob']) => 'binary',
            default => 'string',
        };
    }

    protected function detectDisplayColumn(string $table): ?string
    {
        $columns = $this->getColumns($table);

        foreach (['name', 'title', 'label', 'code', 'slug'] as $candidate) {
            if (isset($columns[$candidate])) {
                return $candidate;
            }
        }

        return collect($columns)
            ->first(fn (array $column, string $name): bool => $column['category'] === 'string' && ! in_array($name, ['id', 'uuid'], true))['name'] ?? null;
    }
}

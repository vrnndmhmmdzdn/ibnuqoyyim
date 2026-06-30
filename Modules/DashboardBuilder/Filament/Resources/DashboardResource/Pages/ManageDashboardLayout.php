<?php

namespace Modules\DashboardBuilder\Filament\Resources\DashboardResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Modules\DashboardBuilder\Filament\Resources\DashboardResource;
use Modules\DashboardBuilder\Filament\Resources\DashboardResource\Schemas\DashboardForm;
use Modules\DashboardBuilder\Models\Dashboard;
use Modules\DashboardBuilder\Services\DashboardChartDataService;
use Modules\DashboardBuilder\Services\DashboardTableSchemaService;

abstract class ManageDashboardLayout extends Page
{
    protected static string $resource = DashboardResource::class;

    protected string $view = 'dashboard-builder::filament.resources.dashboard-resource.pages.dashboard-builder';

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    /**
     * @var array<int, array{id?: int, title: string, sort_order: int, config: array<string, mixed>}>
     */
    public array $charts = [];

    public ?int $selectedChartIndex = null;

    public ?Dashboard $record = null;

    public function mount(?Dashboard $record = null): void
    {
        $this->record = $record;

        if ($record) {
            $this->fillFromRecord($record);
        }

        if ($this->charts === []) {
            $this->addChart();
        }
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            Action::make('dashboardDetails')
                ->label('Dashboard Details')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->fillForm(fn (): array => [
                    'name' => $this->name,
                    'description' => $this->description,
                    'is_active' => $this->isActive,
                ])
                ->schema(DashboardForm::getComponents())
                ->action(function (array $data): void {
                    $this->name = trim((string) ($data['name'] ?? ''));
                    $this->description = (string) ($data['description'] ?? '');
                    $this->isActive = (bool) ($data['is_active'] ?? false);

                    if (! $this->record) {
                        return;
                    }

                    $this->record->fill([
                        'name' => $this->name,
                        'slug' => $this->generateUniqueSlug($this->name, $this->record->getKey()),
                        'description' => $this->description,
                        'is_active' => $this->isActive,
                    ]);

                    $this->record->save();
                    $this->record = $this->record->fresh('charts');

                    Notification::make()
                        ->title('Dashboard details updated')
                        ->success()
                        ->send();
                }),
            Action::make('save')
                ->label('Save Layout')
                ->icon('heroicon-o-check')
                ->action('save'),
        ];

        if ($this->record) {
            $actions[] = Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url($this->record->getPreviewUrl())
                ->openUrlInNewTab();

            $actions[] = Action::make('publish')
                ->label($this->record->is_published ? 'Update Publish' : 'Publish')
                ->icon('heroicon-o-paper-airplane')
                ->fillForm(fn (): array => [
                    'published_requires_login' => $this->record?->published_requires_login ?? false,
                ])
                ->schema([
                    Toggle::make('published_requires_login')
                        ->label('Require Login')
                        ->helperText('If enabled, users must log in before they can open the published dashboard link.'),
                    Placeholder::make('published_link')
                        ->label('Published URL')
                        ->content(function (): HtmlString {
                            $url = $this->record?->getPublishedUrl();

                            if (blank($url)) {
                                return new HtmlString('<span class="text-gray-500">A unique link will be generated when you publish this dashboard.</span>');
                            }

                            return new HtmlString('<a href="'.$url.'" target="_blank" class="text-primary-600 underline">'.$url.'</a>');
                        }),
                ])
                ->modalSubmitActionLabel($this->record->is_published ? 'Update Publish' : 'Publish')
                ->action(function (array $data): void {
                    if (! $this->record) {
                        return;
                    }

                    $this->record->fill([
                        'public_uuid' => $this->record->public_uuid ?: (string) Str::uuid(),
                        'is_published' => true,
                        'published_requires_login' => (bool) Arr::get($data, 'published_requires_login', false),
                        'published_at' => now(),
                    ]);
                    $this->record->save();
                    $this->record = $this->record->fresh('charts');

                    Notification::make()
                        ->title('Dashboard published successfully')
                        ->body($this->record->getPublishedUrl() ?: 'The published link is now ready to use.')
                        ->success()
                        ->persistent()
                        ->send();
                });
        }

        return $actions;
    }

    public function addChart(): void
    {
        $schemaService = app(DashboardTableSchemaService::class);
        $table = $schemaService->getFirstAvailableTable();
        $columns = $table ? $schemaService->getColumns($table) : [];

        $this->charts[] = [
            'title' => 'Chart '.(count($this->charts) + 1),
            'sort_order' => count($this->charts) + 1,
            'config' => [
                'type' => 'bar',
                'table' => $table,
                'dimension' => $table ? $schemaService->getFirstDimension($table) : null,
                'group_by' => $table ? $schemaService->getFirstDimension($table) : null,
                'label_column' => $table ? $schemaService->getFirstDimension($table) : null,
                'value_column' => $schemaService->getFirstNumericColumn($columns),
                'aggregate' => 'count',
                'width' => 'half',
                'ratio' => '16/9',
                'limit' => 10,
                'sort_direction' => 'desc',
            ],
        ];

        $this->selectedChartIndex = array_key_last($this->charts);
        $this->syncSortOrder();
    }

    public function selectChart(int $index): void
    {
        if (! isset($this->charts[$index])) {
            return;
        }

        $this->selectedChartIndex = $index;
    }

    public function removeChart(int $index): void
    {
        if (! isset($this->charts[$index])) {
            return;
        }

        unset($this->charts[$index]);

        $this->charts = array_values($this->charts);
        $this->syncSortOrder();

        if ($this->charts === []) {
            $this->selectedChartIndex = null;

            return;
        }

        $this->selectedChartIndex = min($index, count($this->charts) - 1);
    }

    public function duplicateChart(int $index): void
    {
        if (! isset($this->charts[$index])) {
            return;
        }

        $chart = $this->charts[$index];
        unset($chart['id']);
        $chart['title'] = $chart['title'].' Copy';

        array_splice($this->charts, $index + 1, 0, [$chart]);

        $this->syncSortOrder();
        $this->selectedChartIndex = $index + 1;
    }

    public function moveChartUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->charts[$index])) {
            return;
        }

        [$this->charts[$index - 1], $this->charts[$index]] = [$this->charts[$index], $this->charts[$index - 1]];

        $this->syncSortOrder();
        $this->selectedChartIndex = $index - 1;
    }

    public function moveChartDown(int $index): void
    {
        if (! isset($this->charts[$index + 1])) {
            return;
        }

        [$this->charts[$index + 1], $this->charts[$index]] = [$this->charts[$index], $this->charts[$index + 1]];

        $this->syncSortOrder();
        $this->selectedChartIndex = $index + 1;
    }

    public function save(): void
    {
        $this->validate($this->rules(), $this->messages());

        DB::transaction(function (): void {
            $dashboard = $this->record ?? new Dashboard;

            $dashboard->fill([
                'name' => $this->name,
                'slug' => $this->generateUniqueSlug($this->name, $dashboard->getKey()),
                'description' => $this->description,
                'is_active' => $this->isActive,
            ]);

            $dashboard->save();

            $currentIds = [];

            foreach ($this->charts as $index => $chart) {
                $model = $dashboard->charts()->updateOrCreate(
                    ['id' => $chart['id'] ?? null],
                    [
                        'title' => $chart['title'],
                        'sort_order' => $index + 1,
                        'config_json' => app(DashboardChartDataService::class)->normalizeConfig($chart['config']),
                    ],
                );

                $currentIds[] = $model->id;
            }

            $dashboard->charts()
                ->whereNotIn('id', $currentIds)
                ->delete();

            $this->record = $dashboard->fresh('charts');
            $this->fillFromRecord($this->record);
        });

        Notification::make()
            ->title('Dashboard saved successfully')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record]), navigate: true);
    }

    public function updated(string $name, mixed $value): void
    {
        if (! Str::startsWith($name, 'charts.')) {
            return;
        }

        if (preg_match('/^charts\.(\d+)\.config\.table$/', $name, $matches)) {
            $this->normalizeChartConfig((int) $matches[1]);

            return;
        }

        if (preg_match('/^charts\.(\d+)\.config\.(aggregate|dimension)$/', $name, $matches)) {
            $this->normalizeChartConfig((int) $matches[1]);
        }
    }

    /**
     * @return array<string, string>
     */
    public function getTableOptions(): array
    {
        return app(DashboardTableSchemaService::class)->getTableOptions();
    }

    /**
     * @return array<string, string>
     */
    public function getDimensionOptions(?string $table): array
    {
        return app(DashboardTableSchemaService::class)->getDimensionOptions((string) $table);
    }

    /**
     * @return array<string, string>
     */
    public function getNumericColumnOptions(?string $table): array
    {
        return app(DashboardTableSchemaService::class)->getNumericColumnOptions((string) $table);
    }

    /**
     * @return array<string, string>
     */
    public function getChartTypeOptions(): array
    {
        return [
            'bar' => 'Bar Chart',
            'line' => 'Line Chart',
            'pie' => 'Pie Chart',
            'doughnut' => 'Doughnut Chart',
            'polarArea' => 'Polar Area',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getAggregateOptions(): array
    {
        return [
            'count' => 'Count',
            'sum' => 'Sum',
            'avg' => 'Average',
            'min' => 'Minimum',
            'max' => 'Maximum',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getWidthOptions(): array
    {
        return [
            'third' => '1/3 Grid',
            'half' => '1/2 Grid',
            'two-thirds' => '2/3 Grid',
            'full' => 'Full Width',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getRatioOptions(): array
    {
        return [
            '16/9' => '16:9',
            '4/3' => '4:3',
            '1/1' => '1:1',
            '21/9' => '21:9',
        ];
    }

    /**
     * @return array{type: string, data: array<string, mixed>, options: array<string, mixed>, error: string|null, meta: array<string, mixed>}
     */
    public function getChartPreview(int $index): array
    {
        if (! isset($this->charts[$index])) {
            return app(DashboardChartDataService::class)->build([
                'type' => 'bar',
                'table' => null,
                'dimension' => null,
                'label_column' => null,
                'value_column' => null,
                'aggregate' => 'count',
            ]);
        }

        return app(DashboardChartDataService::class)->build($this->charts[$index]['config'] ?? []);
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

    protected function fillFromRecord(Dashboard $record): void
    {
        $this->name = $record->name;
        $this->description = $record->description ?? '';
        $this->isActive = $record->is_active;
        $this->charts = $record->charts
            ->sortBy('sort_order')
            ->values()
            ->map(fn ($chart): array => [
                'id' => $chart->id,
                'title' => $chart->title,
                'sort_order' => $chart->sort_order,
                'config' => app(DashboardChartDataService::class)->normalizeConfig($chart->config_json ?? []),
            ])
            ->all();

        $this->selectedChartIndex = $this->charts === [] ? null : 0;
    }

    protected function normalizeChartConfig(int $index): void
    {
        if (! isset($this->charts[$index])) {
            return;
        }

        $this->charts[$index]['config'] = app(DashboardChartDataService::class)->normalizeConfig($this->charts[$index]['config'] ?? []);
    }

    protected function syncSortOrder(): void
    {
        foreach ($this->charts as $index => $chart) {
            $this->charts[$index]['sort_order'] = $index + 1;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'isActive' => ['boolean'],
            'charts' => ['array', 'min:1'],
            'charts.*.title' => ['required', 'string', 'max:255'],
            'charts.*.config.type' => ['required', 'string'],
            'charts.*.config.table' => ['required', 'string'],
            'charts.*.config.dimension' => ['required', 'string'],
            'charts.*.config.aggregate' => ['required', 'string'],
            'charts.*.config.width' => ['required', 'string'],
            'charts.*.config.ratio' => ['required', 'string'],
            'charts.*.config.limit' => ['required', 'integer', 'min:1', 'max:20'],
            'charts.*.config.sort_direction' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'charts.min' => 'Add at least one chart to the dashboard.',
            'charts.*.title.required' => 'Each chart must have a title.',
            'charts.*.config.table.required' => 'Select a source table for each chart.',
            'charts.*.config.dimension.required' => 'Select a grouping for each chart.',
        ];
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'dashboard';
        $slug = $base;
        $suffix = 2;

        while (
            Dashboard::query()
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}

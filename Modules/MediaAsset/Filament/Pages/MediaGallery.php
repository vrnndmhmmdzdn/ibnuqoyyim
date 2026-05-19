<?php

namespace Modules\MediaAsset\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Modules\MediaAsset\Filament\Resources\MediaAssetResource;
use Modules\MediaAsset\Models\MediaAsset;
use Modules\MediaAsset\Models\MediaAssetFolder;
use Modules\MediaAsset\Services\MediaAssetService;
use Modules\MediaAsset\Support\MediaAssetUpload;
use UnitEnum;

class MediaGallery extends Page
{
    use WithPagination;

    protected string $view = 'media-asset::filament.pages.media-gallery';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::FolderOpen;

    protected static ?string $navigationLabel = 'Media Gallery';

    protected static string | UnitEnum | null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Media Gallery';

    protected static ?string $slug = 'media-gallery';

    #[Url(as: 'folder')]
    public ?string $currentFolder = null;

    #[Url(as: 'search')]
    public string $search = '';

    public int $perPage = 18;

    public function mount(): void
    {
        $this->currentFolder = app(MediaAssetService::class)->normalizeFolder($this->currentFolder);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openFolder(string $path): void
    {
        $this->currentFolder = app(MediaAssetService::class)->normalizeFolder($path);
        $this->resetPage();
    }

    public function goToRoot(): void
    {
        $this->currentFolder = null;
        $this->resetPage();
    }

    public function goToParentFolder(): void
    {
        if (blank($this->currentFolder)) {
            return;
        }

        $parent = Str::beforeLast($this->currentFolder, '/');
        $this->currentFolder = filled($parent) ? $parent : null;
        $this->resetPage();
    }

    public function moveAssetToFolder(int $assetId, ?string $folder = null): void
    {
        $asset = MediaAsset::query()->findOrFail($assetId);

        $this->moveResolvedAssetToFolder($asset, $folder);
    }

    public function deleteFolder(string $path): void
    {
        $path = app(MediaAssetService::class)->normalizeFolder($path);

        if (blank($path)) {
            return;
        }

        $deletedAssetCount = MediaAsset::query()
            ->where('folder', $path)
            ->orWhere('folder', 'like', $path . '/%')
            ->get()
            ->tap(fn (Collection $assets) => $assets->each->delete())
            ->count();

        $deletedFolderCount = MediaAssetFolder::query()
            ->where('path', $path)
            ->orWhere('path', 'like', $path . '/%')
            ->delete();

        if (filled($this->currentFolder) && ($this->currentFolder === $path || str_starts_with($this->currentFolder, $path . '/'))) {
            $parent = Str::beforeLast($path, '/');
            $this->currentFolder = filled($parent) ? $parent : null;
            $this->resetPage();
        }

        Notification::make()
            ->success()
            ->title('Folder berhasil dihapus')
            ->body(trim(sprintf(
                '%d folder dan %d image dihapus.',
                max(1, $deletedFolderCount),
                $deletedAssetCount,
            )))
            ->send();
    }

    protected function resolveAssetFromArguments(array $arguments): MediaAsset
    {
        return MediaAsset::query()->findOrFail($arguments['asset']);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('createFolder')
                ->label('Folder Baru')
                ->icon(Heroicon::OutlinedFolderPlus)
                ->schema([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->label('Nama Folder')
                        ->required()
                        ->maxLength(255)
                        ->rule('not_regex:/[\/\\\\]/')
                        ->helperText('Gunakan satu nama folder tanpa slash.')
                        ->placeholder('contoh: banners'),
                ])
                ->action(function (array $data, MediaAssetService $service): void {
                    $name = trim($data['name']);
                    $path = $service->buildFolderPath($this->currentFolder, $name);

                    $exists = MediaAssetFolder::query()->where('path', $path)->exists()
                        || MediaAsset::query()->where('folder', $path)->exists()
                        || MediaAsset::query()->where('folder', 'like', $path . '/%')->exists();

                    if ($exists) {
                        Notification::make()
                            ->warning()
                            ->title('Folder sudah ada')
                            ->send();

                        return;
                    }

                    $service->ensureFolderExists($path);

                    Notification::make()
                        ->success()
                        ->title('Folder berhasil dibuat')
                        ->send();
                }),
            Action::make('uploadImage')
                ->label('Upload')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->modalWidth(Width::ThreeExtraLarge)
                ->schema([
                    FileUpload::make('uploaded_image')
                        ->label('Image')
                        ->image()
                        ->multiple()
                        ->required()
                        ->disk(MediaAssetUpload::DEFAULT_DISK)
                        ->directory(MediaAssetUpload::TEMP_DIRECTORY)
                        ->storeFileNamesIn(MediaAssetUpload::FILE_NAMES_FIELD)
                        ->visibility('public')
                        ->imagePreviewHeight('220')
                        ->openable()
                        ->downloadable(),
                ])
                ->action(function (array $data, MediaAssetService $service): void {
                    $uploadedCount = count($service->createAssets($data, $this->currentFolder));

                    Notification::make()
                        ->success()
                        ->title($uploadedCount > 1 ? $uploadedCount . ' image berhasil di-upload' : 'Image berhasil di-upload')
                        ->send();
                }),
            Action::make('manageResource')
                ->label('Table View')
                ->icon(Heroicon::OutlinedListBullet)
                ->url(MediaAssetResource::getUrl()),
            Action::make('deleteCurrentFolder')
                ->label('Hapus Folder Ini')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->hidden(fn (): bool => blank($this->currentFolder))
                ->requiresConfirmation()
                ->modalHeading(fn (): string => 'Hapus folder ' . $this->currentFolderLabel . '?')
                ->modalDescription('Semua subfolder dan image di dalam folder ini akan dihapus permanen.')
                ->modalSubmitActionLabel('Hapus Folder')
                ->action(fn (): mixed => $this->deleteFolder($this->currentFolder)),
        ];
    }

    public function previewAssetAction(): Action
    {
        return Action::make('previewAsset')
            ->label('Preview')
            ->icon(Heroicon::OutlinedEye)
            ->modalHeading(fn (array $arguments): string => $this->resolveAssetFromArguments($arguments)->display_name)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Tutup')
            ->modalWidth(Width::FiveExtraLarge)
            ->modalContent(fn (array $arguments) => view('media-asset::filament.media-asset.preview', [
                'record' => $this->resolveAssetFromArguments($arguments),
            ]));
    }

    public function editAssetAction(): Action
    {
        return Action::make('editAsset')
            ->label('Edit')
            ->icon(Heroicon::OutlinedPencilSquare)
            ->modalWidth(Width::ThreeExtraLarge)
            ->fillForm(function (array $arguments): array {
                $asset = $this->resolveAssetFromArguments($arguments);

                return [
                    'uploaded_image' => $asset->path,
                ];
            })
            ->schema([
                FileUpload::make('uploaded_image')
                    ->label('Image')
                    ->image()
                    ->disk(MediaAssetUpload::DEFAULT_DISK)
                    ->directory(MediaAssetUpload::TEMP_DIRECTORY)
                    ->storeFileNamesIn(MediaAssetUpload::FILE_NAMES_FIELD)
                    ->visibility('public')
                    ->imagePreviewHeight('220')
                    ->openable()
                    ->downloadable(),
            ])
            ->action(function (array $arguments, array $data, MediaAssetService $service): void {
                $service->updateAsset($this->resolveAssetFromArguments($arguments), $data);

                Notification::make()
                    ->success()
                    ->title('Image berhasil diperbarui')
                    ->send();
            });
    }

    public function moveAssetAction(): Action
    {
        return Action::make('moveAsset')
            ->label('Move')
            ->icon(Heroicon::OutlinedFolder)
            ->fillForm(function (array $arguments): array {
                $asset = $this->resolveAssetFromArguments($arguments);

                return [
                    'folder' => $asset->folder ?: '__root__',
                ];
            })
            ->schema([
                Select::make('folder')
                    ->label('Pindah ke Folder')
                    ->options(fn (): array => $this->folderDestinationOptions())
                    ->searchable()
                    ->native(false)
                    ->required(),
            ])
            ->action(function (array $arguments, array $data): void {
                $this->moveResolvedAssetToFolder(
                    $this->resolveAssetFromArguments($arguments),
                    $data['folder'] ?? null,
                );
            });
    }

    public function copyAssetLinkAction(): Action
    {
        return Action::make('copyAssetLink')
            ->label('Copy link')
            ->icon(Heroicon::OutlinedLink)
            ->action(function (array $arguments): void {
                $asset = $this->resolveAssetFromArguments($arguments);

                if (blank($asset->public_url)) {
                    Notification::make()
                        ->warning()
                        ->title('Link belum tersedia')
                        ->send();

                    return;
                }

                $this->dispatch('copy-link', url: $asset->public_url);

                Notification::make()
                    ->success()
                    ->title('Link berhasil disalin')
                    ->send();
            });
    }

    public function deleteAssetAction(): Action
    {
        return Action::make('deleteAsset')
            ->label('Hapus')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments): string => 'Hapus ' . $this->resolveAssetFromArguments($arguments)->display_name . '?')
            ->modalDescription('File image dan record media asset akan dihapus permanen.')
            ->modalSubmitActionLabel('Hapus')
            ->action(function (array $arguments): void {
                $this->resolveAssetFromArguments($arguments)->delete();

                Notification::make()
                    ->success()
                    ->title('Image berhasil dihapus')
                    ->send();
            });
    }

    public function deleteFolderAction(): Action
    {
        return Action::make('deleteFolder')
            ->label('Hapus Folder')
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments): string => 'Hapus folder ' . $this->folderLabel($arguments['path']) . '?')
            ->modalDescription('Semua subfolder dan image di dalam folder ini akan dihapus permanen.')
            ->modalSubmitActionLabel('Hapus Folder')
            ->action(fn (array $arguments): mixed => $this->deleteFolder($arguments['path']));
    }

    public function getBreadcrumbs(): array
    {
        return collect($this->folderCrumbs())
            ->mapWithKeys(fn (array $crumb): array => [$crumb['url'] => $crumb['label']])
            ->all();
    }

    /**
     * @return array<int, array{label: string, path: ?string, url: string}>
     */
    public function folderCrumbs(): array
    {
        $crumbs = [
            [
                'label' => 'Home',
                'path' => null,
                'url' => static::getUrl(),
            ],
        ];

        if (blank($this->currentFolder)) {
            return $crumbs;
        }

        $segments = explode('/', $this->currentFolder);
        $path = null;

        foreach ($segments as $segment) {
            $path = filled($path) ? $path . '/' . $segment : $segment;

            $crumbs[] = [
                'label' => Str::title(str_replace(['-', '_'], ' ', $segment)),
                'path' => $path,
                'url' => static::getUrl(['folder' => $path]),
            ];
        }

        return $crumbs;
    }

    /**
     * @return Collection<int, array{name: string, path: string, asset_count: int, preview_url: ?string}>
     */
    public function getFoldersProperty(): Collection
    {
        $assets = MediaAsset::query()
            ->select(['id', 'folder', 'public_url'])
            ->whereNotNull('folder')
            ->get();

        $paths = MediaAssetFolder::query()
            ->pluck('path')
            ->merge($assets->pluck('folder'))
            ->filter()
            ->unique()
            ->values();

        $prefix = filled($this->currentFolder) ? $this->currentFolder . '/' : null;

        return $paths
            ->map(function (string $path) use ($prefix): ?string {
                if (filled($prefix) && ! str_starts_with($path, $prefix)) {
                    return null;
                }

                if (filled($prefix)) {
                    $remaining = substr($path, strlen($prefix));
                } else {
                    $remaining = $path;
                }

                if (blank($remaining)) {
                    return null;
                }

                $segment = Str::before($remaining, '/');

                return filled($this->currentFolder) ? $this->currentFolder . '/' . $segment : $segment;
            })
            ->filter()
            ->unique()
            ->values()
            ->map(function (string $path) use ($assets): array {
                $name = Str::afterLast($path, '/');

                $matchingAssets = $assets->filter(function (MediaAsset $asset) use ($path): bool {
                    if ($asset->folder === $path) {
                        return true;
                    }

                    return filled($asset->folder) && str_starts_with($asset->folder, $path . '/');
                });

                return [
                    'name' => $name,
                    'path' => $path,
                    'asset_count' => $matchingAssets->count(),
                    'preview_url' => $matchingAssets->first()?->public_url,
                ];
            })
            ->filter(function (array $folder): bool {
                if (blank($this->search)) {
                    return true;
                }

                return str_contains(Str::lower($folder['name']), Str::lower($this->search))
                    || str_contains(Str::lower($folder['path']), Str::lower($this->search));
            })
            ->sortBy('name')
            ->values();
    }

    public function getAssetsProperty(): LengthAwarePaginator
    {
        return MediaAsset::query()
            ->when(
                filled($this->currentFolder),
                fn ($query) => $query->where('folder', $this->currentFolder),
                fn ($query) => $query->whereNull('folder')
            )
            ->when(filled($this->search), function ($query): void {
                $query->where(function ($innerQuery): void {
                    $innerQuery
                        ->where('path', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);
    }

    public function getFolderCountProperty(): int
    {
        return $this->folders->count();
    }

    public function getAssetCountProperty(): int
    {
        return (int) $this->assets->total();
    }

    public function getCurrentFolderLabelProperty(): string
    {
        return filled($this->currentFolder)
            ? Str::title(str_replace(['-', '_', '/'], [' ', ' ', ' / '], $this->currentFolder))
            : 'Home';
    }

    public function getSubheading(): ?string
    {
        return 'Mode browser untuk folder dan gallery image internal.';
    }

    /**
     * @return array<string, string>
     */
    protected function folderDestinationOptions(): array
    {
        $paths = MediaAssetFolder::query()
            ->pluck('path')
            ->merge(MediaAsset::query()->whereNotNull('folder')->pluck('folder'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return ['__root__' => 'Home'] + $paths
            ->mapWithKeys(fn (string $path): array => [$path => Str::title(str_replace(['-', '_', '/'], [' ', ' ', ' / '], $path))])
            ->all();
    }

    protected function moveResolvedAssetToFolder(MediaAsset $asset, ?string $folder = null): void
    {
        $service = app(MediaAssetService::class);
        $folder = $folder === '__root__' ? null : $service->normalizeFolder($folder);

        if ($asset->folder === $folder) {
            Notification::make()
                ->warning()
                ->title('Image sudah ada di folder tersebut')
                ->send();

            return;
        }

        if (filled($folder)) {
            $service->ensureFolderExists($folder);
        }

        $asset->update([
            'folder' => $folder,
        ]);

        $service->syncMetadata($asset);

        Notification::make()
            ->success()
            ->title(filled($folder) ? 'Image dipindahkan ke ' . $this->folderLabel($folder) : 'Image dipindahkan ke Home')
            ->send();
    }

    protected function folderLabel(?string $path): string
    {
        if (blank($path)) {
            return 'Home';
        }

        return Str::title(str_replace(['-', '_', '/'], [' ', ' ', ' / '], $path));
    }
}

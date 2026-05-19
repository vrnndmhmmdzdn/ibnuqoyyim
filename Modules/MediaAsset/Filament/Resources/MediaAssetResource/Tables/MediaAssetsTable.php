<?php

namespace Modules\MediaAsset\Filament\Resources\MediaAssetResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Modules\MediaAsset\Models\MediaAsset;
use Modules\MediaAsset\Services\MediaAssetService;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Preview')
                    ->imageSize(96)
                    ->square(),
                TextColumn::make('display_name')
                    ->label('Filename')
                    ->searchable(query: fn ($query, string $search) => $query->where('path', 'like', '%' . $search . '%'))
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('path', $direction))
                    ->weight('medium')
                    ->limit(40),
                TextColumn::make('display_folder')
                    ->label('Folder')
                    ->badge()
                    ->icon(Heroicon::OutlinedFolder)
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('folder', $direction)),
                TextColumn::make('disk')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('public_url')
                    ->label('URL')
                    ->limit(40)
                    ->copyable()
                    ->copyableState(fn (MediaAsset $record): ?string => $record->public_url)
                    ->copyMessage('URL berhasil disalin')
                    ->placeholder('Belum tersedia'),
                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('folder')
                    ->options(fn (): array => MediaAsset::query()
                        ->whereNotNull('folder')
                        ->orderBy('folder')
                        ->distinct()
                        ->pluck('folder', 'folder')
                        ->all()),
                SelectFilter::make('disk')
                    ->options(fn (): array => MediaAsset::query()
                        ->orderBy('disk')
                        ->distinct()
                        ->pluck('disk', 'disk')
                        ->all()),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon(Heroicon::OutlinedEye)
                    ->modalHeading(fn (MediaAsset $record): string => $record->display_name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth(Width::FiveExtraLarge)
                    ->modalContent(fn (MediaAsset $record) => view('media-asset::filament.media-asset.preview', [
                        'record' => $record,
                    ])),
                Action::make('copyUrl')
                    ->label('Copy URL')
                    ->icon(Heroicon::OutlinedLink)
                    ->hidden(fn (MediaAsset $record): bool => blank($record->public_url))
                    ->fillForm(fn (MediaAsset $record): array => [
                        'public_url' => $record->public_url,
                    ])
                    ->schema([
                        TextInput::make('public_url')
                            ->label('Public URL')
                            ->readOnly()
                            ->copyable()
                            ->dehydrated(false),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth(Width::Large),
                Action::make('moveFolder')
                    ->label('Move Folder')
                    ->icon(Heroicon::OutlinedFolder)
                    ->fillForm(fn (MediaAsset $record): array => [
                        'folder' => $record->folder,
                    ])
                    ->schema([
                        TextInput::make('folder')
                            ->label('Folder')
                            ->placeholder('Kosongkan untuk root')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data, MediaAsset $record, MediaAssetService $service): void {
                        $record->update([
                            'folder' => $service->normalizeFolder($data['folder'] ?? null),
                        ]);

                        $service->syncMetadata($record);

                        Notification::make()
                            ->success()
                            ->title('Folder media diperbarui')
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('moveFolderSelected')
                        ->label('Move Folder Selected')
                        ->icon(Heroicon::OutlinedFolder)
                        ->schema([
                            TextInput::make('folder')
                                ->label('Folder')
                                ->placeholder('Kosongkan untuk root')
                                ->maxLength(255),
                        ])
                        ->action(function (Collection $records, array $data, MediaAssetService $service): void {
                            $folder = $service->normalizeFolder($data['folder'] ?? null);

                            $records->each(function (MediaAsset $record) use ($folder, $service): void {
                                $record->update([
                                    'folder' => $folder,
                                ]);

                                $service->syncMetadata($record);
                            });

                            Notification::make()
                                ->success()
                                ->title('Folder media terpilih diperbarui')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada media')
            ->emptyStateDescription('Upload image pertama untuk mulai mengelola gallery internal.')
            ->emptyStateIcon(Heroicon::OutlinedPhoto);
    }
}

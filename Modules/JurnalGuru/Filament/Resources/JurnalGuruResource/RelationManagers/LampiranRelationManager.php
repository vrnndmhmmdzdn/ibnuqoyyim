<?php

namespace Modules\JurnalGuru\Filament\Resources\JurnalGuruResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Modules\JurnalGuru\Models\JurnalLampiran;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class LampiranRelationManager extends RelationManager
{
    protected static string $relationship = 'lampirans';
    protected static ?string $title = 'Lampiran';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')
                ->label('File')
                ->required()
                ->disk('public')
                ->directory('jurnal-lampirans')
                ->acceptedFileTypes([
                    'image/jpeg', 'image/png', 'image/webp',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                ])
                ->maxSize(10240)
                ->imagePreviewHeight('100')
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state instanceof TemporaryUploadedFile) {
                        $clientName = $state->getClientOriginalName();
                        $ekstensi = pathinfo($clientName, PATHINFO_EXTENSION);
                        
                        $set('nama_file', $clientName);
                        $set('ukuran', $state->getSize());
                        $set('tipe', JurnalLampiran::deteksiTipe($ekstensi));
                    }
                })
                ->columnSpanFull(),

            Select::make('tipe')
                ->label('Tipe Lampiran')
                ->options(JurnalLampiran::TIPE)
                ->required()
                ->native(false),

            Hidden::make('nama_file'),
            Hidden::make('ukuran'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('public')
                    ->height(48)
                    ->width(48)
                    ->defaultImageUrl(asset('images/file-icon.png'))
                    ->visibility('public'),

                TextColumn::make('nama_file')
                    ->label('Nama File')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'foto_kegiatan' => 'info',
                        'rpp'           => 'success',
                        'modul'         => 'warning',
                        'xlsx'          => 'success',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => JurnalLampiran::TIPE[$state] ?? $state),

                TextColumn::make('ukuran_readable')
                    ->label('Ukuran')
                    ->getStateUsing(fn($record) => $record->ukuran_readable),

                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('Tambah Lampiran')
                    ->mutateFormDataUsing(function (array $data): array {
                        if (isset($data['path'])) {
                            if ($data['path'] instanceof TemporaryUploadedFile) {
                                $data['nama_file'] = $data['path']->getClientOriginalName();
                                $data['ukuran']    = $data['path']->getSize();
                            } elseif (is_string($data['path'])) {
                                $data['nama_file'] = $data['nama_file'] ?? basename($data['path']);
                                $data['ukuran']    = $data['ukuran'] ?? 0;
                            }
                        }
                        return $data;
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}   
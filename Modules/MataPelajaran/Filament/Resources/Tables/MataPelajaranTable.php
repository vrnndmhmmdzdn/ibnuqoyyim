<?php

namespace Modules\MataPelajaran\Filament\Resources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\MataPelajaran\Models\MataPelajaran;

class MataPelajaranTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pelajaran')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'Umum'            => 'info',
                        'Agama'       => 'success',
                        'Ekstrakurikuler' => 'warning',
                        default           => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(MataPelajaran::KATEGORI),

                SelectFilter::make('is_aktif')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
                RestoreAction::make(),
                ForceDeleteAction::make(),            
            ])
            
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Mata Pelajaran')
                        ->modalDescription('Data yang dihapus tidak bisa dikembalikan dan bisa merusak relasi ke jurnal guru. Lanjutkan?'),
                    ForceDeleteBulkAction::make(), // hapus permanen
                    RestoreBulkAction::make(), 
                ]),
            ])
            ->defaultSort('kategori');
    }
}
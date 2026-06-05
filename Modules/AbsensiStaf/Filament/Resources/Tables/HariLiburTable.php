<?php

namespace Modules\AbsensiStaf\Filament\Resources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HariLiburTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->label('Hari')
                    ->formatStateUsing(fn($state) =>
                        \Carbon\Carbon::parse($state)->locale('id')->translatedFormat('l')
                    )
                    ->badge()
                    ->color('gray'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),

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
                SelectFilter::make('is_aktif')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'asc')
            ->emptyStateHeading('Belum ada hari libur')
            ->emptyStateDescription('Tambahkan hari libur nasional atau libur sekolah.');
    }
}
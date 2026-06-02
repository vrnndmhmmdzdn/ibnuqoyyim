<?php

namespace Modules\JurnalGuru\Filament\Resources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\JurnalGuru\Models\JurnalGuru;

class JurnalGuruTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('guru.name')
                    ->label('Guru')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info'),

                TextColumn::make('mataPelajaran.pelajaran')
                    ->label('Mata Pelajaran')
                    ->searchable(),

                TextColumn::make('materi')
                    ->label('Materi')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('capaian')
                    ->label('Capaian')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'tercapai' => 'success',
                        'sebagian' => 'warning',
                        'belum'    => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => JurnalGuru::CAPAIAN[$state] ?? $state),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'submitted' => 'success',
                        'draft'     => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => JurnalGuru::STATUS[$state] ?? $state),

                TextColumn::make('jumlah_hadir')
                    ->label('Hadir')
                    ->suffix(' siswa'),
                TextColumn::make('lampirans_count')
                    ->label('Lampiran')
                    ->counts('lampirans')
                    ->badge()
                    ->color(fn(int $state) => $state > 0 ? 'success' : 'gray')
                    ->formatStateUsing(fn(int $state) => $state . ' file'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('capaian')
                    ->label('Capaian')
                    ->options(JurnalGuru::CAPAIAN),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(JurnalGuru::STATUS),

                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas'),

                SelectFilter::make('guru_id')
                    ->label('Guru')
                    ->relationship('guru', 'name'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                RestoreBulkAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }
}
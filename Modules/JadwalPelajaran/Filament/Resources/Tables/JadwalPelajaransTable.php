<?php

namespace Modules\JadwalPelajaran\Filament\Resources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;

class JadwalPelajaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mataPelajaran.pelajaran')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guru.name')
                    ->label('Guru Pengajar')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tahunAjaran.tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hari')
                    ->label('Hari')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jam_mulai')
                    ->label('Waktu Mulai')
                    ->dateTime('H:i')
                    ->sortable(),
                TextColumn::make('jam_selesai')
                    ->label('Waktu Selesai')
                    ->dateTime('H:i')
                    ->sortable(),         
                // TextColumn::make('notes')
                //     ->label('Catatan')
                //     ->limit(30)
                //     ->tooltip(function (TextColumn $column): ?string {
                //         $state = $column->getState();
                //         if (strlen($state) <= 30) {
                //             return null;
                //         }
                //         return $state;
                //     })
                //     ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('hari')
                    ->label('Hari')
                    ->options(JadwalPelajaran::HARI)
                    ->multiple(),
                
                SelectFilter::make('date_range')
                    ->label('Periode')
                    // ->options([
                    //     'today' => 'Hari Ini',
                    //     'tomorrow' => 'Besok',
                    //     'this_week' => 'Minggu Ini',
                    //     'next_week' => 'Minggu Depan',
                    //     'this_month' => 'Bulan Ini',
                    // ])
                    // ->query(function ($query, array $data) {
                    //     if (!$data['value']) {
                    //         return $query;
                    //     }
                        
                    //     return match ($data['value']) {
                    //         'today' => $query->whereDate('jam_mulai', today()),
                    //         'tomorrow' => $query->whereDate('jam_mulai', today()->addDay()),
                    //         'this_week' => $query->whereBetween('jam_mulai', [
                    //             now()->startOfWeek(),
                    //             now()->endOfWeek()
                    //         ]),
                    //         'next_week' => $query->whereBetween('jam_mulai', [
                    //             now()->addWeek()->startOfWeek(),
                    //             now()->addWeek()->endOfWeek()
                    //         ]),
                    //         'this_month' => $query->whereBetween('jam_mulai', [
                    //             now()->startOfMonth(),
                    //             now()->endOfMonth()
                    //         ]),
                    //         default => $query,
                    //     };
                    // }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('jam_mulai', 'desc');
    }
}

<?php

namespace Modules\KalenderDidik\Filament\Resources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\KalenderDidik\Models\Kaldik;

class KaldikTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_acara')
                    ->label('Nama Kegiatan/Acara')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kegiatan')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state) => match($state) {
                        'Ujian'        => 'danger',
                        'Libur'        => 'success',
                        'Akademik'     => 'info',
                        'Non-Akademik' => 'warning',
                        default        => 'gray',
                    }),
                TextColumn::make('subject')
                    ->label('Untuk')
                    ->badge()
                    ->color('info'),

                TextColumn::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('gray'), 

                TextColumn::make('jam_mulai')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('jam_selesai')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),        
                // TextColumn::make('subject')
                //     ->label('Lapangan')
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         'Lap A' => 'success',
                //         'Lap B' => 'info',
                //         'Lap C' => 'warning',
                //         'Lap D' => 'danger',
                //         default => 'gray',
                //     })
                //     ->sortable(),    
                // TextColumn::make('duration')
                //     ->label('Durasi')
                //     ->getStateUsing(fn ($record) => $record->duration . ' jam')
                //     ->sortable(false),
     
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options(Kaldik::KATEGORI),
                    
                SelectFilter::make('subject')
                    ->label('Kelas')
                    ->options(Kaldik::SUBJECTS),

                SelectFilter::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->options(fn() =>
                        \Modules\TahunAjaran\Models\TahunAjaran::pluck('tahun_ajaran', 'tahun_ajaran')
                    ),
                SelectFilter::make('date_range')
                    ->label('Periode')
                    ->options([
                        'today' => 'Hari Ini',
                        'tomorrow' => 'Besok',
                        'this_week' => 'Minggu Ini',
                        'next_week' => 'Minggu Depan',
                        'this_month' => 'Bulan Ini',
                    ])
                    ->query(function ($query, array $data) {
                        if (!$data['value']) {
                            return $query;
                        }
                        
                        return match ($data['value']) {
                            'today' => $query->whereDate('jam_mulai', today()),
                            'tomorrow' => $query->whereDate('jam_mulai', today()->addDay()),
                            'this_week' => $query->whereBetween('jam_mulai', [
                                now()->startOfWeek(),
                                now()->endOfWeek()
                            ]),
                            'next_week' => $query->whereBetween('jam_mulai', [
                                now()->addWeek()->startOfWeek(),
                                now()->addWeek()->endOfWeek()
                            ]),
                            'this_month' => $query->whereBetween('jam_mulai', [
                                now()->startOfMonth(),
                                now()->endOfMonth()
                            ]),
                            default => $query,
                        };
                    }),
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

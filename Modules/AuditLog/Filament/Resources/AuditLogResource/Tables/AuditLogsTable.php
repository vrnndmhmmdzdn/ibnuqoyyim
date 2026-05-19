<?php

namespace Modules\AuditLog\Filament\Resources\AuditLogResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\AuditLog\Models\AuditLog;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('auditable_type')
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '-')
                    ->searchable(),
                TextColumn::make('auditable_id')
                    ->label('Record ID')
                    ->searchable(),
                TextColumn::make('actor.name')
                    ->label('Actor')
                    ->default('-')
                    ->searchable(),
                TextColumn::make('method')
                    ->badge()
                    ->default('-')
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'restored' => 'Restored',
                    ]),
                SelectFilter::make('auditable_type')
                    ->label('Model')
                    ->options(function () {
                        return AuditLog::query()
                            ->select('auditable_type')
                            ->distinct()
                            ->pluck('auditable_type')
                            ->mapWithKeys(fn ($item) => [$item => class_basename($item)])
                            ->toArray();
                    }),
                SelectFilter::make('actor_type')
                    ->label('Actor Type')
                    ->options(function () {
                        return AuditLog::query()
                            ->select('actor_type')
                            ->whereNotNull('actor_type')
                            ->distinct()
                            ->pluck('actor_type')
                            ->mapWithKeys(fn ($item) => [$item => class_basename($item)])
                            ->toArray();
                    }),
                SelectFilter::make('method')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Date From'),
                        DatePicker::make('until')->label('Date To'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
                Filter::make('actor_id')
                    ->form([
                        TextInput::make('actor_id')->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['actor_id'] ?? null, fn ($q, $value) => $q->where('actor_id', $value));
                    }),
                Filter::make('auditable_id')
                    ->form([
                        TextInput::make('auditable_id')->label('Record ID'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['auditable_id'] ?? null, fn ($q, $value) => $q->where('auditable_id', $value));
                    }),
                Filter::make('url_contains')
                    ->form([
                        TextInput::make('url')->label('URL Contains'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['url'] ?? null, fn ($q, $value) => $q->where('url', 'like', '%' . $value . '%'));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

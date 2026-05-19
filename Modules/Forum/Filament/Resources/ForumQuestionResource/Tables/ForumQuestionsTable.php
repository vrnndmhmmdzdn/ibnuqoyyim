<?php

namespace Modules\Forum\Filament\Resources\ForumQuestionResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ForumQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(80),
                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('comments_count')
                    ->label('Comments')
                    ->badge()
                    ->sortable(),
                TextColumn::make('images')
                    ->label('Images')
                    ->getStateUsing(fn ($record): int => count((array) $record->images))
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? $state . ' gambar' : 'Tanpa gambar')
                    ->badge()
                    ->icon(fn (int $state): ?string => $state > 0 ? 'heroicon-m-photo' : null)
                    ->color(fn (int $state): string => $state > 0 ? 'warning' : 'gray')
                    ->sortable(false),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

<?php

namespace Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Modules\TelegramAssistant\Models\TelegramUser;

class TelegramUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->placeholder('Belum diisi'),
                TextColumn::make('chat_id')
                    ->label('Chat ID')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TelegramUser::ROLES[$state] ?? $state),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('last_interaction_at')
                    ->label('Interaksi Terakhir')
                    ->dateTime()
                    ->since()
                    ->placeholder('Belum pernah'),
            ])
            ->filters([
                SelectFilter::make('role')->options(TelegramUser::ROLES),
                TernaryFilter::make('is_active')->label('Status Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada user Telegram terdaftar')
            ->emptyStateDescription('Tambahkan chat_id guru/admin agar bisa menggunakan asisten Telegram.');
    }
}
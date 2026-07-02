<?php

namespace Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\TelegramAssistant\Models\TelegramUser;

class TelegramUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('chat_id')
                    ->label('Chat ID Telegram')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Dapatkan dari bot @userinfobot di Telegram.'),
                TextInput::make('name')
                    ->label('Nama')
                    ->maxLength(255),
                Select::make('role')
                    ->label('Role')
                    ->options(TelegramUser::ROLES)
                    ->required()
                    ->native(false),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
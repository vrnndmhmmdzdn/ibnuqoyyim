<?php

namespace Modules\TelegramAssistant\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Pages\CreateTelegramUser;
use Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Pages\EditTelegramUser;
use Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Pages\ListTelegramUsers;
use Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Schemas\TelegramUserForm;
use Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Tables\TelegramUsersTable;
use Modules\TelegramAssistant\Models\TelegramUser;
use UnitEnum;

class TelegramUserResource extends Resource
{
    protected static ?string $model = TelegramUser::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::PaperAirplane;

    protected static string | UnitEnum | null $navigationGroup = 'Asisten';

    protected static ?string $navigationLabel = 'Telegram Users';

    public static function form(Schema $schema): Schema
    {
        return TelegramUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TelegramUsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTelegramUsers::route('/'),
            'create' => CreateTelegramUser::route('/create'),
            'edit' => EditTelegramUser::route('/{record}/edit'),
        ];
    }
}
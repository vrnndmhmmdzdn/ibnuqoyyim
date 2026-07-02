<?php

namespace Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\TelegramAssistant\Filament\Resources\TelegramUserResource;

class EditTelegramUser extends EditRecord
{
    protected static string $resource = TelegramUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
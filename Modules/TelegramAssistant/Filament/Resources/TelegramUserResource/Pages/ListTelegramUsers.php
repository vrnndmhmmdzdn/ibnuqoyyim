<?php

namespace Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\TelegramAssistant\Filament\Resources\TelegramUserResource;

class ListTelegramUsers extends ListRecords
{
    protected static string $resource = TelegramUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
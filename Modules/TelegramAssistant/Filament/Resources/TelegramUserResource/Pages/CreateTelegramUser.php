<?php

namespace Modules\TelegramAssistant\Filament\Resources\TelegramUserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\TelegramAssistant\Filament\Resources\TelegramUserResource;

class CreateTelegramUser extends CreateRecord
{
    protected static string $resource = TelegramUserResource::class;
}
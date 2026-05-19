<?php

namespace Modules\KalenderDidik\Filament\Resources\Pages;

use Modules\KalenderDidik\Filament\Resources\KaldikResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKaldik extends EditRecord
{
    protected static string $resource = KaldikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

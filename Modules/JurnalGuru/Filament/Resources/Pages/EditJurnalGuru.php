<?php

namespace Modules\JurnalGuru\Filament\Resources\Pages;

use Modules\JurnalGuru\Filament\Resources\JurnalGuruResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJurnalGuru extends EditRecord
{
    protected static string $resource = JurnalGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

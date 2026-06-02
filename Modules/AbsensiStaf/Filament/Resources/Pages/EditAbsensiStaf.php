<?php

namespace Modules\AbsensiStaf\Filament\Resources\Pages;

use Modules\AbsensiStaf\Filament\Resources\AbsensiStafResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiStaf extends EditRecord
{
    protected static string $resource = AbsensiStafResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

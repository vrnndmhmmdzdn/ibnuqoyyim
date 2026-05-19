<?php

namespace Modules\Angkatan\Filament\Resources\Pages;

use Modules\Angkatan\Filament\Resources\AngkatanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAngkatan extends EditRecord
{
    protected static string $resource = AngkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

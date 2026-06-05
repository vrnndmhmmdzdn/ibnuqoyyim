<?php

namespace Modules\AbsensiStaf\Filament\Resources\Pages;

use Modules\AbsensiStaf\Filament\Resources\HariLiburResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHariLibur extends EditRecord
{
    protected static string $resource = HariLiburResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
<?php

namespace Modules\AbsensiStaf\Filament\Resources\Pages;

use Modules\AbsensiStaf\Filament\Resources\AbsensiStafResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAbsensiStafs extends ListRecords
{
    protected static string $resource = AbsensiStafResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

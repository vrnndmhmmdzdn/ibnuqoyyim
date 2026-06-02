<?php

namespace Modules\JurnalGuru\Filament\Resources\Pages;

use Modules\JurnalGuru\Filament\Resources\JurnalGuruResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJurnalGurus extends ListRecords
{
    protected static string $resource = JurnalGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

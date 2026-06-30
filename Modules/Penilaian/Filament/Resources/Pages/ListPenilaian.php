<?php

namespace Modules\Penilaian\Filament\Resources\Pages;

use Modules\Penilaian\Filament\Resources\PenilaianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenilaian extends ListRecords
{
    protected static string $resource = PenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

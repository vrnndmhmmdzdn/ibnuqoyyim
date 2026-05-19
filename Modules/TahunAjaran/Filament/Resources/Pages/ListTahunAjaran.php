<?php

namespace Modules\TahunAjaran\Filament\Resources\Pages;

use Modules\TahunAjaran\Filament\Resources\TahunAjaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTahunAjaran extends ListRecords
{
    protected static string $resource = TahunAjaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

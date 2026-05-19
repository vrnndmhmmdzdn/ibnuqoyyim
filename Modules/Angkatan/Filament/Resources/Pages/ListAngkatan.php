<?php

namespace Modules\Angkatan\Filament\Resources\Pages;

use Modules\Angkatan\Filament\Resources\AngkatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAngkatan extends ListRecords
{
    protected static string $resource = AngkatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

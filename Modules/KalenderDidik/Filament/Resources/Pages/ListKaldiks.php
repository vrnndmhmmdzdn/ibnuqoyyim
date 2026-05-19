<?php

namespace Modules\KalenderDidik\Filament\Resources\Pages;

use Modules\KalenderDidik\Filament\Resources\KaldikResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKaldiks extends ListRecords
{
    protected static string $resource = KaldikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

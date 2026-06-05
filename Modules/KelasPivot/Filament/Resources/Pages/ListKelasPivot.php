<?php

namespace Modules\KelasPivot\Filament\Resources\Pages;

use Modules\KelasPivot\Filament\Resources\KelasPivotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKelasPivot extends ListRecords
{
    protected static string $resource = KelasPivotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

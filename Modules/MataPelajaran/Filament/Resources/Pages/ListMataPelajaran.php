<?php

namespace Modules\MataPelajaran\Filament\Resources\Pages;

use Modules\MataPelajaran\Filament\Resources\MataPelajaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMataPelajaran extends ListRecords
{
    protected static string $resource = MataPelajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

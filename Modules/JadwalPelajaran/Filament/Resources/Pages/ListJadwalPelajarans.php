<?php

namespace Modules\JadwalPelajaran\Filament\Resources\Pages;

use Modules\JadwalPelajaran\Filament\Resources\JadwalPelajaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalPelajarans extends ListRecords
{
    protected static string $resource = JadwalPelajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

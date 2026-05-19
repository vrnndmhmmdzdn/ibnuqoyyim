<?php

namespace Modules\JadwalPelajaran\Filament\Resources\Pages;

use Modules\JadwalPelajaran\Filament\Resources\JadwalPelajaranResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJadwalPelajaran extends EditRecord
{
    protected static string $resource = JadwalPelajaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

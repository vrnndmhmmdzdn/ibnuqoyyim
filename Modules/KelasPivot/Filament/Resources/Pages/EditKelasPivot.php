<?php

namespace Modules\KelasPivot\Filament\Resources\Pages;

use Modules\KelasPivot\Filament\Resources\KelasPivotResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditKelasPivot extends EditRecord
{
    protected static string $resource = KelasPivotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

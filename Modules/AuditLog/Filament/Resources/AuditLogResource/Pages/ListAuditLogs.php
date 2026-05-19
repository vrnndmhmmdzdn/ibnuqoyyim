<?php

namespace Modules\AuditLog\Filament\Resources\AuditLogResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Modules\AuditLog\Filament\Resources\AuditLogResource;
use Modules\AuditLog\Models\AuditLog;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tableSize')
                ->label('Table Size: ' . AuditLog::humanTableSize())
                ->color('gray')
                ->disabled(),
            Action::make('totalRows')
                ->label('Rows: ' . number_format(AuditLog::query()->count()))
                ->color('gray')
                ->disabled(),
        ];
    }
}

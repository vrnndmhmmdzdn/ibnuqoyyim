<?php

namespace Modules\AuditLog\Filament\Resources\AuditLogResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Modules\AuditLog\Filament\Resources\AuditLogResource;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}

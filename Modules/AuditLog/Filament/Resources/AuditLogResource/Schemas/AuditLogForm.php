<?php

namespace Modules\AuditLog\Filament\Resources\AuditLogResource\Schemas;

use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        // Audit logs are read-only.
        return $schema->schema([]);
    }
}

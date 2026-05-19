<?php

namespace Modules\AuditLog\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\AuditLog\Filament\Resources\AuditLogResource\Infolists\AuditLogInfolist;
use Modules\AuditLog\Filament\Resources\AuditLogResource\Pages\ListAuditLogs;
use Modules\AuditLog\Filament\Resources\AuditLogResource\Pages\ViewAuditLog;
use Modules\AuditLog\Filament\Resources\AuditLogResource\Schemas\AuditLogForm;
use Modules\AuditLog\Filament\Resources\AuditLogResource\Tables\AuditLogsTable;
use Modules\AuditLog\Models\AuditLog;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static string | UnitEnum | null $navigationGroup = 'Audit Logs';

    protected static ?string $navigationLabel = 'Audit Logs';

    public static function form(Schema $schema): Schema
    {
        return AuditLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditLogInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
            'view' => ViewAuditLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}

<?php

namespace Modules\AbsensiStaf\Filament\Resources;

use Modules\AbsensiStaf\Filament\Resources\Pages\CreateAbsensiStaf;
use Modules\AbsensiStaf\Filament\Resources\Pages\EditAbsensiStaf;
use Modules\AbsensiStaf\Filament\Resources\Pages\ListAbsensiStafs;
use Modules\AbsensiStaf\Filament\Resources\Schemas\AbsensiStafForm;
use Modules\AbsensiStaf\Filament\Resources\Tables\AbsensiStafTable;
use Modules\AbsensiStaf\Models\AbsensiStaf;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use UnitEnum;

class AbsensiStafResource extends Resource
{
    protected static ?string $model = AbsensiStaf::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'Absensi Staf';

    protected static ?string $navigationLabel = 'List AbsensiStaf';

    protected static string | UnitEnum | null $navigationGroup = 'Absensi Pendidikan';

    public static function form(Schema $schema): Schema
    {
        return AbsensiStafForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensiStafTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbsensiStafs::route('/'),
            'create' => CreateAbsensiStaf::route('/create'),
            'edit' => EditAbsensiStaf::route('/{record}/edit'),
        ];
    }
}

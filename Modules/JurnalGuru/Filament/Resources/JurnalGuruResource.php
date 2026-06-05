<?php

namespace Modules\JurnalGuru\Filament\Resources;

use Modules\JurnalGuru\Filament\Resources\Pages\CreateJurnalGuru;
use Modules\JurnalGuru\Filament\Resources\Pages\EditJurnalGuru;
use Modules\JurnalGuru\Filament\Resources\Pages\ListJurnalGurus;
use Modules\JurnalGuru\Filament\Resources\Schemas\JurnalGuruForm;
use Modules\JurnalGuru\Filament\Resources\Tables\JurnalGuruTable;
use Modules\JurnalGuru\Models\JurnalGuru;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use UnitEnum;

class JurnalGuruResource extends Resource
{
    protected static ?string $model = JurnalGuru::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Identification;

    protected static ?string $recordTitleAttribute = 'jadwal';

    protected static ?string $navigationLabel = ' Jurnal Guru';

    protected static string | UnitEnum | null $navigationGroup = 'Jurnal';

    public static function form(Schema $schema): Schema
    {
        return JurnalGuruForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JurnalGuruTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJurnalGurus::route('/'),
            'create' => CreateJurnalGuru::route('/create'),
            'edit' => EditJurnalGuru::route('/{record}/edit'),
        ];
    }
    public static function getRelations(): array
    {
        return [
            \Modules\JurnalGuru\Filament\Resources\JurnalGuruResource\RelationManagers\LampiranRelationManager::class,
        ];
    }
}

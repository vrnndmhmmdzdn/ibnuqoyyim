<?php

namespace Modules\Penilaian\Filament\Resources;

use Modules\Penilaian\Filament\Resources\Pages\CreatePenilaian;
use Modules\Penilaian\Filament\Resources\Pages\EditPenilaian;
use Modules\Penilaian\Filament\Resources\Pages\ListPenilaian;
use Modules\Penilaian\Filament\Resources\Schemas\PenilaianForm;
use Modules\Penilaian\Filament\Resources\Tables\PenilaianTable;
use Modules\Penilaian\Models\Penilaian;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenilaianResource extends Resource
{
    protected static ?string $model = Penilaian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    protected static ?string $navigationLabel = 'Data Penilaian  ';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return PenilaianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenilaianTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            // 'index' => ListPenilaian::route('/'),
            // 'create' => CreatePenilaian::route('/create'),
            // 'edit' => EditPenilaian::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

<?php

namespace Modules\KelasPivot\Filament\Resources;

use Modules\KelasPivot\Filament\Resources\Pages\CreateKelasPivot;
use Modules\KelasPivot\Filament\Resources\Pages\EditKelasPivot;
use Modules\KelasPivot\Filament\Resources\Pages\ListKelasPivot;
use Modules\KelasPivot\Filament\Resources\Schemas\KelasPivotForm;
use Modules\KelasPivot\Filament\Resources\Tables\KelasPivotTable;
use Modules\KelasPivot\Models\KelasPivot;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelasPivotResource extends Resource
{
    protected static ?string $model = KelasPivot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Link;

    protected static ?string $navigationLabel = 'Kelas Pivot';

    protected static string | UnitEnum | null $navigationGroup = 'Kelas & Siswa';

    public static function form(Schema $schema): Schema
    {
        return KelasPivotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KelasPivotTable::configure($table);
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
            'index' => ListKelasPivot::route('/'),
            'create' => CreateKelasPivot::route('/create'),
            'edit' => EditKelasPivot::route('/{record}/edit'),
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

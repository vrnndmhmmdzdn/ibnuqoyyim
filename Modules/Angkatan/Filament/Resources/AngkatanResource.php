<?php

namespace Modules\Angkatan\Filament\Resources;

use Modules\Angkatan\Filament\Resources\Pages\CreateAngkatan;
use Modules\Angkatan\Filament\Resources\Pages\EditAngkatan;
use Modules\Angkatan\Filament\Resources\Pages\ListAngkatan;
use Modules\Angkatan\Filament\Resources\Schemas\AngkatanForm;
use Modules\Angkatan\Filament\Resources\Tables\AngkatanTable;
use Modules\Angkatan\Models\Angkatan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AngkatanResource extends Resource
{
    protected static ?string $model = Angkatan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::NumberedList;

    protected static ?string $navigationLabel = 'Angkatan';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return AngkatanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AngkatanTable::configure($table);
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
            'index' => ListAngkatan::route('/'),
            'create' => CreateAngkatan::route('/create'),
            'edit' => EditAngkatan::route('/{record}/edit'),
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

<?php

namespace Modules\Kelas\Filament\Resources;

use Modules\Kelas\Filament\Resources\Pages\CreateKelas;
use Modules\Kelas\Filament\Resources\Pages\EditKelas;
use Modules\Kelas\Filament\Resources\Pages\ListKelas;
use Modules\Kelas\Filament\Resources\Schemas\KelasForm;
use Modules\Kelas\Filament\Resources\Tables\KelasTable;
use Modules\Kelas\Models\Kelas;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::NumberedList;

    protected static ?string $navigationLabel = 'Kelas';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return KelasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KelasTable::configure($table);
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
            'index' => ListKelas::route('/'),
            'create' => CreateKelas::route('/create'),
            'edit' => EditKelas::route('/{record}/edit'),
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

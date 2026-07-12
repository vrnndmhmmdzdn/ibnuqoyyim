<?php

namespace Modules\Siswa\Filament\Resources;

use Modules\Siswa\Filament\Resources\Pages\CreateSiswa;
use Modules\Siswa\Filament\Resources\Pages\EditSiswa;
use Modules\Siswa\Filament\Resources\Pages\ListSiswa;
use Modules\Siswa\Filament\Resources\Schemas\SiswaForm;
use Modules\Siswa\Filament\Resources\Tables\SiswaTable;
use Modules\Siswa\Models\Siswa;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $navigationLabel = 'Data Siswa  ';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return SiswaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiswaTable::configure($table);
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
            'index' => ListSiswa::route('/'),
            'create' => CreateSiswa::route('/create'),
            'edit' => EditSiswa::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function canAccess(): bool
    {
        return auth()->user()->role == 'admin';
    }
}

<?php

namespace Modules\MataPelajaran\Filament\Resources;

use Modules\MataPelajaran\Filament\Resources\Pages\CreateMataPelajaran;
use Modules\MataPelajaran\Filament\Resources\Pages\EditMataPelajaran;
use Modules\MataPelajaran\Filament\Resources\Pages\ListMataPelajaran;
use Modules\MataPelajaran\Filament\Resources\Schemas\MataPelajaranForm;
use Modules\MataPelajaran\Filament\Resources\Tables\MataPelajaranTable;
use Modules\MataPelajaran\Models\MataPelajaran;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MataPelajaranResource extends Resource
{
    protected static ?string $model = MataPelajaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    protected static ?string $navigationLabel = 'Mata Pelajaran';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return MataPelajaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MataPelajaranTable::configure($table);
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
            'index' => ListMataPelajaran::route('/'),
            'create' => CreateMataPelajaran::route('/create'),
            'edit' => EditMataPelajaran::route('/{record}/edit'),
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

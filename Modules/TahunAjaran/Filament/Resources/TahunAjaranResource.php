<?php

namespace Modules\TahunAjaran\Filament\Resources;

use Modules\TahunAjaran\Filament\Resources\Pages\CreateTahunAjaran;
use Modules\TahunAjaran\Filament\Resources\Pages\EditTahunAjaran;
use Modules\TahunAjaran\Filament\Resources\Pages\ListTahunAjaran;
use Modules\TahunAjaran\Filament\Resources\Schemas\TahunAjaranForm;
use Modules\TahunAjaran\Filament\Resources\Tables\TahunAjaranTable;
use Modules\TahunAjaran\Models\TahunAjaran;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TahunAjaranResource extends Resource
{
    protected static ?string $model = TahunAjaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Tahun Ajaran  ';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    public static function form(Schema $schema): Schema
    {
        return TahunAjaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TahunAjaranTable::configure($table);
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
            'index' => ListTahunAjaran::route('/'),
            'create' => CreateTahunAjaran::route('/create'),
            'edit' => EditTahunAjaran::route('/{record}/edit'),
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

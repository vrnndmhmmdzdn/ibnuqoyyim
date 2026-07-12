<?php

namespace Modules\AbsensiStaf\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\AbsensiStaf\Filament\Resources\Pages\CreateHariLibur;
use Modules\AbsensiStaf\Filament\Resources\Pages\EditHariLibur;
use Modules\AbsensiStaf\Filament\Resources\Pages\ListHariLiburs;
use Modules\AbsensiStaf\Filament\Resources\Schemas\HariLiburForm;
use Modules\AbsensiStaf\Filament\Resources\Tables\HariLiburTable;
use Modules\AbsensiStaf\Models\HariLibur;
use UnitEnum;

class HariLiburResource extends Resource
{
    protected static ?string $model = HariLibur::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?string $navigationLabel = 'Hari Libur';
    protected static string|UnitEnum|null $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 6;
    protected static ?string $modelLabel = 'Hari Libur';
    protected static ?string $pluralModelLabel = 'Hari Libur';

    public static function form(Schema $schema): Schema
    {
        return HariLiburForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HariLiburTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListHariLiburs::route('/'),
            'create' => CreateHariLibur::route('/create'),
            'edit'   => EditHariLibur::route('/{record}/edit'),
        ];
    }
    public static function canAccess(): bool
    {
        return auth()->user()->role == 'admin';
    }
}
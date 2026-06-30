<?php

namespace Modules\DashboardBuilder\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\DashboardBuilder\Filament\Resources\DashboardResource\Pages;
use Modules\DashboardBuilder\Filament\Resources\DashboardResource\Schemas\DashboardForm;
use Modules\DashboardBuilder\Filament\Resources\DashboardResource\Tables\DashboardsTable;
use Modules\DashboardBuilder\Models\Dashboard;
use UnitEnum;

class DashboardResource extends Resource
{
    protected static ?string $model = Dashboard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Dashboard Builder';

    protected static ?string $navigationLabel = 'Dashboard Builder';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return DashboardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DashboardsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDashboards::route('/'),
            'view' => Pages\ViewDashboard::route('/{record}'),
            'edit' => Pages\EditDashboard::route('/{record}/edit'),
        ];
    }
}

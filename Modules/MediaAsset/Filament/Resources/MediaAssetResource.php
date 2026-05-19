<?php

namespace Modules\MediaAsset\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\MediaAsset\Filament\Resources\MediaAssetResource\Pages\CreateMediaAsset;
use Modules\MediaAsset\Filament\Resources\MediaAssetResource\Pages\EditMediaAsset;
use Modules\MediaAsset\Filament\Resources\MediaAssetResource\Pages\ListMediaAssets;
use Modules\MediaAsset\Filament\Resources\MediaAssetResource\Schemas\MediaAssetForm;
use Modules\MediaAsset\Filament\Resources\MediaAssetResource\Tables\MediaAssetsTable;
use Modules\MediaAsset\Models\MediaAsset;
use UnitEnum;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Photo;

    protected static string | UnitEnum | null $navigationGroup = 'Media';

    protected static ?string $navigationLabel = 'Media Assets';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MediaAssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaAssetsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaAssets::route('/'),
            'create' => CreateMediaAsset::route('/create'),
            'edit' => EditMediaAsset::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('media');
    }
}

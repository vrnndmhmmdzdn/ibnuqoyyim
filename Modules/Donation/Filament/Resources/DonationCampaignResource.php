<?php

namespace Modules\Donation\Filament\Resources;

use Modules\Donation\Filament\Resources\DonationCampaignResource\Pages;
use Modules\Donation\Filament\Resources\DonationCampaignResource\Schemas\DonationCampaignForm;
use Modules\Donation\Filament\Resources\DonationCampaignResource\Tables\DonationCampaignsTable;
use Modules\Donation\Filament\Resources\DonationCampaignResource\Infolists\DonationCampaignInfolist;
use Modules\Donation\Models\DonationCampaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DonationCampaignResource extends Resource
{
    protected static ?string $model = DonationCampaign::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Flag;

    protected static string | UnitEnum | null $navigationGroup = 'Donasi';

    public static function form(Schema $schema): Schema
    {
        return DonationCampaignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationCampaignsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonationCampaignInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonationCampaigns::route('/'),
            'create' => Pages\CreateDonationCampaign::route('/create'),
            'view' => Pages\ViewDonationCampaign::route('/{record}'),
            'edit' => Pages\EditDonationCampaign::route('/{record}/edit'),
        ];
    }
}

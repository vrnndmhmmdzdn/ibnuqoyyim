<?php

namespace Modules\Donation\Filament\Resources;

use Modules\Donation\Filament\Resources\DonationDonationResource\Pages;
use Modules\Donation\Filament\Resources\DonationDonationResource\Schemas\DonationDonationForm;
use Modules\Donation\Filament\Resources\DonationDonationResource\Tables\DonationDonationsTable;
use Modules\Donation\Filament\Resources\DonationDonationResource\Infolists\DonationDonationInfolist;
use Modules\Donation\Models\DonationDonation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DonationDonationResource extends Resource
{
    protected static ?string $model = DonationDonation::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::Heart;

    protected static string | UnitEnum | null $navigationGroup = 'Donasi';

    public static function form(Schema $schema): Schema
    {
        return DonationDonationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationDonationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonationDonationInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonationDonations::route('/'),
            'view' => Pages\ViewDonationDonation::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

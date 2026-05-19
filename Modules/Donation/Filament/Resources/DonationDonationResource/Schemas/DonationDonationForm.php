<?php

namespace Modules\Donation\Filament\Resources\DonationDonationResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class DonationDonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('provider_order_id')->disabled(),
            Forms\Components\TextInput::make('donor_name')->disabled(),
            Forms\Components\TextInput::make('donor_email')->disabled(),
            Forms\Components\TextInput::make('amount')->disabled(),
            Forms\Components\TextInput::make('status')->disabled(),
            Forms\Components\Textarea::make('message')->disabled(),
        ]);
    }
}

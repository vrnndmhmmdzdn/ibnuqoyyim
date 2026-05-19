<?php

namespace Modules\Donation\Filament\Resources\DonationCampaignResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DonationCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(150)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('slug', Str::slug($state));
                }),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')
                ->required()
                ->rows(6),
            Forms\Components\FileUpload::make('cover_image_path')
                ->image()
                ->disk('public')
                ->visibility('public')
                ->directory('donation/campaigns')
                ->imagePreviewHeight('160'),
            Forms\Components\TextInput::make('contact_name')
                ->label('Contact Person')
                ->maxLength(100),
            Forms\Components\TextInput::make('contact_phone')
                ->label('Contact Phone/WA')
                ->maxLength(30),
            Forms\Components\TextInput::make('target_amount')
                ->numeric()
                ->required()
                ->minValue(1000),
            Forms\Components\DateTimePicker::make('deadline_at'),
            Forms\Components\Select::make('status')
                ->required()
                ->options([
                    'draft' => 'Draft',
                    'active' => 'Active',
                    'closed' => 'Closed',
                ]),
        ]);
    }
}

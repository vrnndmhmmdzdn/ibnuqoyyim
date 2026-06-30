<?php

namespace Modules\DashboardBuilder\Filament\Resources\DashboardResource\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DashboardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(static::getComponents())
            ->columns(1);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public static function getComponents(): array
    {
        return [
            TextInput::make('name')
                ->label('Dashboard Name')
                ->required()
                ->maxLength(255)
                ->placeholder('Executive Summary'),
            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->placeholder('Daily operational dashboard for the team.')
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label('Dashboard Active')
                ->default(true),
        ];
    }
}

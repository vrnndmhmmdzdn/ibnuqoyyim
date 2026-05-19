<?php

namespace Modules\Donation\Filament\Resources\DonationCampaignResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DonationCampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->url(fn ($record) => url('/donation/' . $record->slug))
                    ->openUrlInNewTab(),
                TextColumn::make('slug')
                    ->label('Landing URL')
                    ->formatStateUsing(fn ($state) => '/donation/' . $state)
                    ->url(fn ($record) => url('/donation/' . $record->slug))
                    ->openUrlInNewTab(),
                TextColumn::make('status')->badge(),
                TextColumn::make('target_amount')->money('idr', locale: 'id'),
                TextColumn::make('deadline_at')->dateTime(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'active' => 'Active',
                    'closed' => 'Closed',
                ]),
            ])
            ->recordActions([
                Action::make('viewLandingPage')
                    ->label('Open Public Page')
                    ->color('gray')
                    ->url(fn ($record) => url('/donation/' . $record->slug))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

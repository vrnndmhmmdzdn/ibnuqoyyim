<?php

namespace Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Pages;

use Modules\Midtrans\Filament\Resources\MidtransTransactionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListMidtransTransactions extends ListRecords
{
    protected static string $resource = MidtransTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Sample Transaction')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}

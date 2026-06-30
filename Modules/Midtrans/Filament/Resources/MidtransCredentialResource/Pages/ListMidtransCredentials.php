<?php

namespace Modules\Midtrans\Filament\Resources\MidtransCredentialResource\Pages;

use Modules\Midtrans\Filament\Resources\MidtransCredentialResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListMidtransCredentials extends ListRecords
{
    protected static string $resource = MidtransCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

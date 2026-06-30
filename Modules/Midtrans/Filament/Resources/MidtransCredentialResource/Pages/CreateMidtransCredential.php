<?php

namespace Modules\Midtrans\Filament\Resources\MidtransCredentialResource\Pages;

use Modules\Midtrans\Filament\Resources\MidtransCredentialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMidtransCredential extends CreateRecord
{
    protected static string $resource = MidtransCredentialResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Midtrans credential berhasil dibuat';
    }
}

<?php

namespace Modules\Midtrans\Filament\Resources\MidtransCredentialResource\Pages;

use Modules\Midtrans\Filament\Resources\MidtransCredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMidtransCredential extends EditRecord
{
    protected static string $resource = MidtransCredentialResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!config('midtrans.demo_mode')) {
            return $data;
        }

        $record = $this->record;

        if ($record->server_key !== null) {
            unset($data['server_key']);
        }
        if ($record->client_key !== null) {
            unset($data['client_key']);
        }
        if ($record->merchant_id !== null) {
            unset($data['merchant_id']);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Midtrans credential berhasil diperbarui';
    }
}

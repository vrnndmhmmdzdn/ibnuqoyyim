<?php

namespace Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Midtrans\Filament\Resources\MidtransTransactionResource;
use Modules\Midtrans\Services\MidtransService;
use Modules\Midtrans\Models\MidtransCredential;
use Modules\Midtrans\Models\MidtransTransaction;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class CreateMidtransTransaction extends CreateRecord
{
    protected static string $resource = MidtransTransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Generate order ID jika belum ada
        if (empty($data['order_id'])) {
            $data['order_id'] = 'ORDER-' . strtoupper(Str::random(10)) . '-' . time();
        }

        // Ambil credential yang dipilih atau aktif
        $credential = null;
        if (!empty($data['midtrans_credential_id'])) {
            $credential = MidtransCredential::find($data['midtrans_credential_id']);
        } else {
            $credential = MidtransCredential::getActiveCredential();
        }

        if (!$credential) {
            Notification::make()
                ->title('Error')
                ->body('Tidak ada credential Midtrans yang aktif. Silakan aktifkan credential terlebih dahulu.')
                ->danger()
                ->send();

            $this->halt();
        }

        try {
            // Inisialisasi Midtrans Service
            $midtransService = new MidtransService($credential);

            // Prepare item details
            $itemDetails = [];
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $itemDetails[] = [
                        'id' => $item['id'] ?? 'item-' . rand(1000, 9999),
                        'price' => $item['price'] ?? 0,
                        'quantity' => $item['quantity'] ?? 1,
                        'name' => $item['name'] ?? 'Item',
                    ];
                }
            } else {
                // Default item jika tidak ada
                $itemDetails[] = [
                    'id' => 'sample-item',
                    'price' => $data['gross_amount'],
                    'quantity' => 1,
                    'name' => $data['item_name'] ?? 'Sample Payment',
                ];
            }

            // Prepare customer details
            $customerDetails = [
                'first_name' => $data['customer_name'] ?? 'Customer',
                'email' => $data['customer_email'] ?? 'customer@example.com',
                'phone' => $data['customer_phone'] ?? '08123456789',
            ];

            // Create Snap Transaction via service (this creates the record in DB)
            $transaction = $midtransService->createSnapTransaction(
                orderId: $data['order_id'],
                grossAmount: $data['gross_amount'],
                itemDetails: $itemDetails,
                customerDetails: $customerDetails
            );

            Notification::make()
                ->title('Transaction Created')
                ->body('Transaction berhasil dibuat. Redirecting ke Midtrans payment...')
                ->success()
                ->send();

            // Return transaction yang sudah dibuat oleh service
            return $transaction;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Creating Transaction')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        // Redirect ke view page dengan parameter untuk trigger popup
        return $this->getResource()::getUrl('view', ['record' => $this->record->id]) . '?show_payment=1';
    }
}

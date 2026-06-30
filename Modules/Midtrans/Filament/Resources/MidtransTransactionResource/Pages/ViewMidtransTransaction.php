<?php

namespace Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Pages;

use Modules\Midtrans\Filament\Resources\MidtransTransactionResource;
use Filament\Resources\Pages\ViewRecord;
use Modules\Midtrans\Services\MidtransService;
use Filament\Actions;
use Filament\Actions\Action;

class ViewMidtransTransaction extends ViewRecord
{
    protected static string $resource = MidtransTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('payNow')
                ->label('Pay Now')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->visible(fn() => $this->record->snap_token && in_array($this->record->transaction_status, ['pending', null]))
                ->url(fn() => $this->getResource()::getUrl('view', ['record' => $this->record->id]) . '?show_payment=1'),
        ];
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        // Check if we should show payment popup
        $showPayment = request()->query('show_payment');

        if ($showPayment && $this->record->snap_token) {
            $midtransService = new MidtransService($this->record->credential);
            $clientKey = $midtransService->getClientKey();
            $environment = $midtransService->getEnvironment();
            $snapUrl = $environment === 'production'
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js';

            return view('midtrans::filament.midtrans-payment-popup', [
                'snapToken' => $this->record->snap_token,
                'clientKey' => $clientKey,
                'snapUrl' => $snapUrl,
                'orderId' => $this->record->order_id,
            ]);
        }

        return parent::getFooter();
    }
}

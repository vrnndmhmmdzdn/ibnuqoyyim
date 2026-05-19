<?php

namespace Modules\Donation\Services;

use Modules\Donation\Models\DonationDonation;
use Illuminate\Support\Arr;
use Modules\Midtrans\Services\MidtransService;

class DonationMidtransService
{
    public function __construct(
        protected MidtransService $midtransService
    ) {
    }

    public function createSnap(DonationDonation $donation): array
    {
        $itemDetails = [
            [
                'id' => (string) $donation->campaign_id,
                'price' => (int) $donation->amount,
                'quantity' => 1,
                'name' => $donation->campaign?->title ?? 'Donation',
            ],
        ];

        $customerDetails = [];
        if ($donation->donor_name || $donation->donor_email) {
            $customerDetails = [
                'first_name' => $donation->donor_name ?: 'Donatur',
                'email' => $donation->donor_email ?: null,
            ];
        }

        $transaction = $this->midtransService->createSnapTransaction(
            $donation->provider_order_id,
            (float) $donation->amount,
            $itemDetails,
            $customerDetails,
            [
                'custom_field1' => 'donation',
                'custom_field2' => (string) $donation->id,
                'callbacks' => [
                    'finish' => route('donation.status.redirect'),
                    'unfinish' => route('donation.status.redirect'),
                    'error' => route('donation.status.redirect'),
                ],
            ]
        );

        return [
            'snap_token' => $transaction->snap_token,
            'snap_url' => $transaction->snap_url,
            'raw' => Arr::only($transaction->toArray(), ['id', 'order_id', 'transaction_status']),
        ];
    }

    public function getClientKey(): ?string
    {
        return $this->midtransService->getClientKey();
    }

    public function getEnvironment(): string
    {
        return $this->midtransService->getEnvironment();
    }

    public function handleNotification(array $notification)
    {
        return $this->midtransService->handleNotification($notification);
    }

    public function getTransactionStatus(string $orderId): array
    {
        return $this->midtransService->getTransactionStatus($orderId);
    }

    public function mapToDonationStatus(?string $status, ?string $fraudStatus): string
    {
        if (in_array($status, ['settlement', 'capture'], true)) {
            if ($status === 'capture' && $fraudStatus === 'challenge') {
                return 'pending';
            }

            return 'paid';
        }

        return match ($status) {
            'pending' => 'pending',
            'expire' => 'expired',
            'cancel', 'deny', 'failure' => 'failed',
            'refund', 'partial_refund' => 'refunded',
            default => 'pending',
        };
    }

    public function buildDonationUpdateData(DonationDonation $donation, array $payload): array
    {
        $status = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $mappedStatus = $this->mapToDonationStatus($status, $fraudStatus);

        if ($donation->status === 'paid' && $mappedStatus !== 'paid' && $mappedStatus !== 'refunded') {
            $mappedStatus = 'paid';
        }

        $updateData = [
            'status' => $mappedStatus,
            'provider_transaction_id' => $payload['transaction_id'] ?? $donation->provider_transaction_id,
            'provider_payment_type' => $payload['payment_type'] ?? $donation->provider_payment_type,
            'provider_raw_response' => $payload,
        ];

        if ($mappedStatus === 'paid' && !$donation->paid_at) {
            $updateData['paid_at'] = now();
        }

        return $updateData;
    }
}

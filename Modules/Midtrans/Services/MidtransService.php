<?php

namespace Modules\Midtrans\Services;

use Modules\Midtrans\Models\MidtransCredential;
use Modules\Midtrans\Models\MidtransTransaction;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected $credential;
    protected $serverKey;
    protected $clientKey;
    protected $environment;
    protected $isProduction;

    public function __construct(?MidtransCredential $credential = null)
    {
        if ($credential) {
            $this->credential = $credential;
        } else {
            // Ambil credential aktif (prioritas: production, lalu sandbox)
            $this->credential = MidtransCredential::where('is_active', true)->first();
        }

        if ($this->credential) {
            $this->serverKey = $this->credential->server_key;
            $this->clientKey = $this->credential->client_key;
            $this->environment = $this->credential->environment;
            $this->isProduction = $this->environment === 'production';
        }
    }

    /**
     * Get Snap API URL
     */
    protected function getSnapApiUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Get Core API URL
     */
    protected function getCoreApiUrl(): string
    {
        return $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create Snap Transaction
     *
     * @param string $orderId
     * @param float $grossAmount
     * @param array $itemDetails
     * @param array $customerDetails
     * @param array $additionalParams
     * @return MidtransTransaction
     */
    public function createSnapTransaction(
        string $orderId,
        float $grossAmount,
        array $itemDetails = [],
        array $customerDetails = [],
        array $additionalParams = []
    ): MidtransTransaction {
        if (!$this->credential) {
            throw new Exception('Midtrans credential tidak ditemukan. Pastikan ada credential yang aktif.');
        }

        // Prepare transaction data
        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
        ];

        $params = array_merge([
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ], $additionalParams);

        // Add callback URLs - use credential values or default to app URLs
        $params['callbacks'] = [
            'finish' => $this->credential->finish_url ?: url('/midtrans/finish'),
            'unfinish' => $this->credential->unfinish_url ?: url('/midtrans/unfinish'),
            'error' => $this->credential->error_url ?: url('/midtrans/error'),
        ];

        try {
            // Call Midtrans Snap API
            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($this->getSnapApiUrl(), $params);

            if ($response->failed()) {
                throw new Exception('Midtrans API Error: ' . $response->body());
            }

            $result = $response->json();

            // Save transaction to database
            $transaction = MidtransTransaction::create([
                'midtrans_credential_id' => $this->credential->id,
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'snap_token' => $result['token'] ?? null,
                'snap_url' => $result['redirect_url'] ?? null,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'metadata' => $additionalParams,
                'raw_response' => json_encode($result),
                'transaction_status' => 'pending',
            ]);

            Log::info('Midtrans Snap Transaction Created', [
                'order_id' => $orderId,
                'transaction_id' => $transaction->id,
            ]);

            return $transaction;
        } catch (Exception $e) {
            Log::error('Midtrans Create Transaction Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get Transaction Status
     *
     * @param string $orderId
     * @return array
     */
    public function getTransactionStatus(string $orderId): array
    {
        if (!$this->credential) {
            throw new Exception('Midtrans credential tidak ditemukan.');
        }

        try {
            $url = $this->getCoreApiUrl() . '/' . $orderId . '/status';

            $response = Http::withBasicAuth($this->serverKey, '')
                ->get($url);

            if ($response->failed()) {
                throw new Exception('Midtrans API Error: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Midtrans Get Status Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle Notification dari Midtrans
     *
     * @param array $notification
     * @return MidtransTransaction|null
     */
    public function handleNotification(array $notification): ?MidtransTransaction
    {
        try {
            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;
            $paymentType = $notification['payment_type'] ?? null;
            $transactionId = $notification['transaction_id'] ?? null;
            $transactionTime = $notification['transaction_time'] ?? null;

            if (!$orderId) {
                throw new Exception('Order ID tidak ditemukan dalam notifikasi');
            }

            // Verify signature untuk keamanan
            $this->verifySignature($notification);

            // Find transaction
            $transaction = MidtransTransaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                Log::warning('Transaction not found for notification', ['order_id' => $orderId]);
                return null;
            }

            // Prepare update data
            $updateData = [
                'transaction_id' => $transactionId,
                'payment_type' => $paymentType,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'transaction_time' => $transactionTime ? date('Y-m-d H:i:s', strtotime($transactionTime)) : null,
                'raw_response' => json_encode($notification),
            ];
            
            // Only set updated_via if not already set (first update wins)
            if (!$transaction->updated_via) {
                $updateData['updated_via'] = 'webhook';
                $updateData['status_updated_at'] = now();
            }

            // Update transaction
            $transaction->update($updateData);

            Log::info('✅ WEBHOOK: Update data dari Webhook Midtrans', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
                'payment_type' => $paymentType,
                'updated_via' => $transaction->updated_via,
            ]);

            return $transaction;
        } catch (Exception $e) {
            Log::error('Midtrans Notification Error', [
                'error' => $e->getMessage(),
                'notification' => $notification,
            ]);

            throw $e;
        }
    }

    /**
     * Verify Signature dari Midtrans
     *
     * @param array $notification
     * @return bool
     */
    protected function verifySignature(array $notification): bool
    {
        $orderId = $notification['order_id'] ?? '';
        $statusCode = $notification['status_code'] ?? '';
        $grossAmount = $notification['gross_amount'] ?? '';
        $signatureKey = $notification['signature_key'] ?? '';

        $mySignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        if ($mySignature !== $signatureKey) {
            throw new Exception('Invalid signature');
        }

        return true;
    }

    /**
     * Cancel Transaction
     *
     * @param string $orderId
     * @return array
     */
    public function cancelTransaction(string $orderId): array
    {
        if (!$this->credential) {
            throw new Exception('Midtrans credential tidak ditemukan.');
        }

        try {
            $url = $this->getCoreApiUrl() . '/' . $orderId . '/cancel';

            $response = Http::withBasicAuth($this->serverKey, '')
                ->post($url);

            if ($response->failed()) {
                throw new Exception('Midtrans API Error: ' . $response->body());
            }

            $result = $response->json();

            // Update local transaction
            $transaction = MidtransTransaction::where('order_id', $orderId)->first();
            if ($transaction) {
                $transaction->update([
                    'transaction_status' => 'cancel',
                    'raw_response' => json_encode($result),
                ]);
            }

            Log::info('Midtrans Transaction Cancelled', ['order_id' => $orderId]);

            return $result;
        } catch (Exception $e) {
            Log::error('Midtrans Cancel Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get Snap Token untuk order tertentu
     *
     * @param string $orderId
     * @return string|null
     */
    public function getSnapToken(string $orderId): ?string
    {
        $transaction = MidtransTransaction::where('order_id', $orderId)->first();
        return $transaction?->snap_token;
    }

    /**
     * Get Client Key
     *
     * @return string|null
     */
    public function getClientKey(): ?string
    {
        return $this->clientKey;
    }

    /**
     * Get Environment
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment ?? 'sandbox';
    }
}

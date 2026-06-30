<?php

namespace Modules\Midtrans\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Modules\Midtrans\Services\MidtransService;
use Modules\Midtrans\Models\MidtransCredential;
use UnitEnum;

class MidtransDemo extends Page
{
    protected string $view = 'midtrans::filament.pages.midtrans-demo';

    public ?string $currentOrderId = null;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Midtrans Demo';

    protected static string|UnitEnum|null $navigationGroup = 'Midtrans';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Midtrans Payment Demo';

    protected static ?string $slug = 'midtrans-demo';

    public array $products = [
        [
            'id' => 'BAJU-001',
            'name' => 'Kaos Polos Hitam',
            'description' => 'Kaos polos berbahan cotton combed 30s, nyaman dipakai sehari-hari',
            'price' => 85000,
            'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400',
        ],
        [
            'id' => 'BAJU-002',
            'name' => 'Kemeja Flannel',
            'description' => 'Kemeja flannel motif kotak-kotak, cocok untuk hangout atau kerja casual',
            'price' => 175000,
            'image' => 'https://images.unsplash.com/photo-1589310243389-96a5483213a8?w=400',
        ],
        [
            'id' => 'BAJU-003',
            'name' => 'Hoodie Premium',
            'description' => 'Hoodie premium dengan bahan fleece tebal, hangat dan stylish',
            'price' => 250000,
            'image' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400',
        ],
    ];

    public function mount(): void
    {
        $credential = MidtransCredential::where('is_active', true)->first();
        
        if (!$credential) {
            Notification::make()
                ->title('Midtrans Credential Tidak Ditemukan')
                ->body('Silakan tambahkan credential Midtrans terlebih dahulu.')
                ->warning()
                ->persistent()
                ->send();
        }
    }

    public function buyProduct(string $productId): void
    {
        $product = collect($this->products)->firstWhere('id', $productId);
        
        if (!$product) {
            Notification::make()
                ->title('Produk Tidak Ditemukan')
                ->danger()
                ->send();
            return;
        }

        try {
            $midtransService = app(MidtransService::class);
            
            $orderId = 'DEMO-' . time() . '-' . rand(1000, 9999);
            
            $itemDetails = [
                [
                    'id' => $product['id'],
                    'price' => $product['price'],
                    'quantity' => 1,
                    'name' => $product['name'],
                ],
            ];
            
            $customerDetails = [
                'first_name' => 'Demo',
                'last_name' => 'User',
                'email' => 'demo@example.com',
                'phone' => '08123456789',
            ];
            
            $transaction = $midtransService->createSnapTransaction(
                $orderId,
                $product['price'],
                $itemDetails,
                $customerDetails
            );
            
            $credential = MidtransCredential::getActiveCredential();
            
            $this->currentOrderId = $orderId;
            
            $this->dispatch('openMidtransPopup', [
                'snapToken' => $transaction->snap_token,
                'orderId' => $orderId,
                'snapUrl' => $credential->environment === 'production' 
                    ? 'https://app.midtrans.com/snap/snap.js' 
                    : 'https://app.sandbox.midtrans.com/snap/snap.js',
                'clientKey' => $credential->client_key,
            ]);
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Membuat Transaksi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function closePayment(): void
    {
        // Called from JS when payment popup is closed
    }

    /**
     * Check transaction status from Midtrans API and update database
     */
    public function checkTransactionStatus(string $orderId): void
    {
        try {
            // Small delay to ensure Midtrans has processed the transaction
            usleep(500000); // 0.5 second
            
            $midtransService = app(MidtransService::class);
            $status = $midtransService->getTransactionStatus($orderId);
            
            \Illuminate\Support\Facades\Log::info('Midtrans Status Check', [
                'order_id' => $orderId,
                'status' => $status,
            ]);
            
            // Update transaction in database
            $transaction = \Modules\Midtrans\Models\MidtransTransaction::where('order_id', $orderId)->first();
            
            if ($transaction) {
                $updateData = [
                    'transaction_id' => $status['transaction_id'] ?? null,
                    'payment_type' => $status['payment_type'] ?? null,
                    'transaction_status' => $status['transaction_status'] ?? 'pending',
                    'fraud_status' => $status['fraud_status'] ?? null,
                    'transaction_time' => isset($status['transaction_time']) 
                        ? date('Y-m-d H:i:s', strtotime($status['transaction_time'])) 
                        : null,
                    'raw_response' => json_encode($status),
                ];
                
                // Only set updated_via if not already set (first update wins)
                if (!$transaction->updated_via) {
                    $updateData['updated_via'] = 'ajax';
                    $updateData['status_updated_at'] = now();
                }
                
                $transaction->update($updateData);
                
                $statusText = match($status['transaction_status'] ?? 'pending') {
                    'capture', 'settlement' => 'Berhasil',
                    'pending' => 'Pending',
                    'deny', 'cancel', 'expire' => 'Gagal/Dibatalkan',
                    default => ucfirst($status['transaction_status'] ?? 'Unknown'),
                };
                
                Notification::make()
                    ->title('Status Transaksi: ' . $statusText)
                    ->body('Order ID: ' . $orderId)
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            // Log error for debugging
            \Illuminate\Support\Facades\Log::warning('Status check failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            
            // Show notification that status check failed but payment may still be processed
            Notification::make()
                ->title('Cek Status Gagal')
                ->body('Status akan diupdate via webhook. Order: ' . $orderId)
                ->warning()
                ->send();
        }
    }
}

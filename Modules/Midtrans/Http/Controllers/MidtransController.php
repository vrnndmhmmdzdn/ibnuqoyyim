<?php

namespace Modules\Midtrans\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Midtrans\Services\MidtransService;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle notification dari Midtrans
     */
    public function notification(Request $request)
    {
        try {
            $notification = $request->all();
            
            Log::info('Midtrans Notification Received', $notification);

            $transaction = $this->midtransService->handleNotification($notification);

            if ($transaction) {
                // Di sini bisa ditambahkan logic untuk update status order, dll
                // Event bisa di-dispatch untuk di-handle di module lain
                
                return response()->json([
                    'success' => true,
                    'message' => 'Notification processed successfully',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle finish page
     */
    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');
        $status = null;
        
        // Update status from Midtrans API (via redirect callback)
        if ($orderId) {
            $status = $this->updateTransactionStatus($orderId, 'callback');
        }
        
        return view('midtrans::finish', [
            'order_id' => $orderId,
            'status' => $status,
            'message' => 'Pembayaran Anda berhasil!',
        ]);
    }

    /**
     * Handle unfinish page
     */
    public function unfinish(Request $request)
    {
        $orderId = $request->get('order_id');
        $status = null;
        
        // Update status from Midtrans API (via redirect callback)
        if ($orderId) {
            $status = $this->updateTransactionStatus($orderId, 'callback');
        }
        
        return view('midtrans::unfinish', [
            'order_id' => $orderId,
            'status' => $status,
            'message' => 'Pembayaran belum selesai',
        ]);
    }

    /**
     * Handle error page
     */
    public function error(Request $request)
    {
        $orderId = $request->get('order_id');
        $status = null;
        
        // Update status from Midtrans API (via redirect callback)
        if ($orderId) {
            $status = $this->updateTransactionStatus($orderId, 'callback');
        }
        
        return view('midtrans::error', [
            'order_id' => $orderId,
            'status' => $status,
            'message' => 'Terjadi kesalahan dalam proses pembayaran',
        ]);
    }
    
    /**
     * Update transaction status from Midtrans API
     * @param string $orderId
     * @param string $source - 'callback', 'manual', 'ajax'
     */
    protected function updateTransactionStatus(string $orderId, string $source = 'callback'): ?array
    {
        try {
            $status = $this->midtransService->getTransactionStatus($orderId);
            
            $transaction = \Modules\Midtrans\Models\MidtransTransaction::where('order_id', $orderId)->first();
            
            if ($transaction) {
                // Only update if not already updated via webhook
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
                
                // Only set updated_via if not already set by webhook
                if (!$transaction->updated_via) {
                    $updateData['updated_via'] = $source;
                    $updateData['status_updated_at'] = now();
                }
                
                $transaction->update($updateData);
                
                Log::info("📡 Status updated via {$source}", [
                    'order_id' => $orderId,
                    'status' => $status['transaction_status'] ?? 'unknown',
                    'updated_via' => $transaction->updated_via,
                ]);
            }
            
            return $status;
        } catch (\Exception $e) {
            Log::warning('Failed to update transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }
    
    /**
     * Check and update transaction status via AJAX
     */
    public function checkStatus(string $orderId)
    {
        $status = $this->updateTransactionStatus($orderId, 'ajax');
        
        if ($status) {
            return response()->json([
                'success' => true,
                'status' => $status['transaction_status'] ?? 'unknown',
                'data' => $status,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to get transaction status',
        ], 400);
    }
    
    /**
     * Show callback error page when user lands on wrong URL
     */
    public function callbackError(Request $request)
    {
        $orderId = $request->get('order_id');
        $transactionStatus = $request->get('transaction_status');
        
        // Try to update status if order_id exists
        if ($orderId) {
            $this->updateTransactionStatus($orderId, 'callback');
        }
        
        return view('midtrans::callback-error', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'app_url' => config('app.url'),
            'current_url' => $request->fullUrl(),
        ]);
    }
}

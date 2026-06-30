<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h1>
        <p class="text-gray-600 mb-6">{{ $message }}</p>
        
        @if($order_id)
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-500">Order ID</p>
                <p class="font-mono font-semibold text-gray-800">{{ $order_id }}</p>
            </div>
        @endif
        
        @if($status ?? null)
            <div class="bg-green-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-semibold text-green-600">{{ ucfirst($status['transaction_status'] ?? 'Unknown') }}</p>
                @if(isset($status['payment_type']))
                    <p class="text-sm text-gray-500 mt-1">via {{ ucfirst($status['payment_type']) }}</p>
                @endif
            </div>
        @endif
        
        <a href="/admin/midtrans-demo" class="inline-block w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors" style="background-color: #7c3aed;">
            Kembali ke Demo
        </a>
    </div>
</body>
</html>

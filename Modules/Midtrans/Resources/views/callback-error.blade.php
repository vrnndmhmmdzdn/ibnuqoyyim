<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Callback URL Error - Midtrans</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-lg w-full">
        {{-- Warning Icon --}}
        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">Callback URL Tidak Sesuai</h1>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-yellow-800">
                <strong>Masalah:</strong> Anda mengakses halaman callback Midtrans melalui URL yang berbeda dari aplikasi Laravel.
            </p>
        </div>

        <div class="space-y-4 text-sm text-gray-600">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="font-semibold text-gray-700 mb-2">🔍 Penyebab:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Transaksi dibuat dengan callback URL: <code class="bg-gray-200 px-1 rounded">localhost</code></li>
                    <li>Tapi Laravel berjalan di: <code class="bg-gray-200 px-1 rounded">127.0.0.1:8000</code></li>
                </ul>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="font-semibold text-blue-700 mb-2">✅ Solusi:</p>
                <ol class="list-decimal list-inside space-y-2">
                    <li>
                        <strong>Buat transaksi baru</strong> - Transaksi baru akan menggunakan callback URL yang benar
                    </li>
                    <li>
                        <strong>Atau set di Midtrans Dashboard:</strong>
                        <ul class="list-disc list-inside ml-4 mt-1 text-gray-600">
                            <li>Login ke <a href="https://dashboard.sandbox.midtrans.com" target="_blank" class="text-blue-600 underline">Midtrans Dashboard</a></li>
                            <li>Settings → Configuration</li>
                            <li>Set Finish/Unfinish/Error Redirect URL ke:<br>
                                <code class="bg-gray-200 px-1 rounded text-xs">{{ $app_url }}/midtrans/finish</code>
                            </li>
                        </ul>
                    </li>
                </ol>
            </div>

            @if($order_id)
            <div class="bg-green-50 rounded-lg p-4">
                <p class="font-semibold text-green-700 mb-2">📋 Info Transaksi:</p>
                <p><strong>Order ID:</strong> <code class="bg-gray-200 px-1 rounded">{{ $order_id }}</code></p>
                @if($transaction_status)
                <p><strong>Status:</strong> <code class="bg-gray-200 px-1 rounded">{{ $transaction_status }}</code></p>
                @endif
                <p class="text-xs mt-2 text-gray-500">
                    Meskipun redirect gagal, pembayaran Anda tetap diproses. Status akan diupdate via webhook atau cek manual.
                </p>
            </div>
            @endif
        </div>
        
        <div class="mt-6 space-y-3">
            <a href="{{ $app_url }}/admin/midtrans-demo" 
               class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                Kembali ke Demo
            </a>
            
            @if($order_id)
            <a href="{{ $app_url }}/admin/midtrans-transactions" 
               class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors">
                Lihat Semua Transaksi
            </a>
            @endif
        </div>
        
        <p class="text-xs text-gray-400 text-center mt-6">
            APP_URL: {{ $app_url }}<br>
            Current URL: {{ $current_url }}
        </p>
    </div>
</body>
</html>

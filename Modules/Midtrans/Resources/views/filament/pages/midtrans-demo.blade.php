<x-filament-panels::page>
    {{-- Product Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($products as $product)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
                {{-- Product Image --}}
                <div class="aspect-video overflow-hidden">
                    <img 
                        src="{{ $product['image'] }}" 
                        alt="{{ $product['name'] }}"
                        class="w-full h-full object-cover"
                    >
                </div>

                {{-- Product Info --}}
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                        {{ $product['name'] }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                        {{ $product['description'] }}
                    </p>
                    
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xl font-bold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($product['price'], 0, ',', '.') }}
                        </span>
                    </div>

                    <button 
                        wire:click="buyProduct('{{ $product['id'] }}')"
                        wire:loading.attr="disabled"
                        class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white font-medium rounded-lg"
                    >
                        <span wire:loading.remove wire:target="buyProduct('{{ $product['id'] }}')">Beli Sekarang</span>
                        <span wire:loading wire:target="buyProduct('{{ $product['id'] }}')">Memproses...</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Payment Info Demo --}}
    <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">
                    Informasi Akun Demo Midtrans
                </h3>
                <div class="space-y-3 text-sm text-blue-800 dark:text-blue-200">
                    <p>
                        <strong>Page ini masih menggunakan akun demo Midtrans.</strong><br>
                        Untuk melakukan simulasi pembayaran, akses simulator resmi Midtrans di sini:
                    </p>
                    <a href="https://simulator.sandbox.midtrans.com" target="_blank" class="inline-block mt-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                        Simulator Midtrans
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Midtrans Snap Script --}}
    @script
    <script>
        window.midtransLoaded = false;
        window.currentOrderId = null;
        
        function loadMidtransSnap(snapUrl, clientKey, snapToken, orderId) {
            window.currentOrderId = orderId;
            
            if (window.midtransLoaded && typeof window.snap !== 'undefined') {
                window.snap.pay(snapToken, paymentCallbacks());
                return;
            }
            
            const script = document.createElement('script');
            script.src = snapUrl;
            script.setAttribute('data-client-key', clientKey);
            script.onload = function() {
                window.midtransLoaded = true;
                window.snap.pay(snapToken, paymentCallbacks());
            };
            document.head.appendChild(script);
        }
        
        function paymentCallbacks() {
            return {
                onSuccess: function(result) {
                    new FilamentNotification().title('Pembayaran Berhasil!').success().send();
                    checkAndClose();
                },
                onPending: function(result) {
                    new FilamentNotification().title('Pembayaran Pending').warning().send();
                    checkAndClose();
                },
                onError: function(result) {
                    new FilamentNotification().title('Pembayaran Gagal').danger().send();
                    checkAndClose();
                },
                onClose: function() {
                    checkAndClose();
                }
            };
        }
        
        function checkAndClose() {
            // Check transaction status from Midtrans API to update database
            if (window.currentOrderId) {
                console.log('Checking status for order:', window.currentOrderId);
                $wire.checkTransactionStatus(window.currentOrderId).then(() => {
                    console.log('Status check completed');
                }).catch((error) => {
                    console.error('Status check error:', error);
                });
            }
            $wire.closePayment();
        }
        
        $wire.on('openMidtransPopup', (data) => {
            const { snapToken, orderId, snapUrl, clientKey } = data[0];
            loadMidtransSnap(snapUrl, clientKey, snapToken, orderId);
        });
    </script>
    @endscript
</x-filament-panels::page>

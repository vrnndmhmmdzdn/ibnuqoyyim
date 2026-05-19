<div class="mx-auto w-full max-w-3xl rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        <h1 class="text-2xl font-semibold text-slate-900 sm:text-3xl">Pembayaran Donasi</h1>
        <p class="mt-2 text-sm text-slate-500">Selesaikan pembayaran lewat Midtrans. Setelah berhasil akan diarahkan ke status donasi.</p>

        <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 sm:p-5">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <span>Campaign</span>
                <span class="text-slate-900 sm:max-w-[65%] sm:text-right">{{ $campaign->title }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between gap-4 sm:mt-2">
                <span>Nominal</span>
                <span class="shrink-0 text-slate-900">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
            </div>
            <div class="mt-3 flex flex-col gap-1 sm:mt-2 sm:flex-row sm:items-start sm:justify-between">
                <span>Order ID</span>
                <span class="break-all font-mono text-xs text-slate-900 sm:max-w-[65%] sm:text-right sm:text-sm">{{ $donation->provider_order_id }}</span>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <button id="pay-now" class="inline-flex w-full items-center justify-center rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600 sm:w-auto">
                Bayar Sekarang
            </button>
            <a href="{{ route('donation.status', $donation->provider_order_id) }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-600 hover:border-orange-400 hover:text-orange-600 sm:w-auto sm:border-0 sm:px-0 sm:py-0">Cek status donasi</a>
        </div>
    </div>

    <script type="text/javascript" src="{{ $environment === 'production' ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.getElementById('pay-now').addEventListener('click', function () {
            if (typeof window.snap === 'undefined') {
                return alert('Midtrans Snap belum siap, silakan refresh halaman.');
            }

            window.snap.pay(@json($snapToken), {
                onSuccess: function () {
                    window.location.href = @json(route('donation.status', $donation->provider_order_id));
                },
                onPending: function () {
                    window.location.href = @json(route('donation.status', $donation->provider_order_id));
                },
                onError: function () {
                    window.location.href = @json(route('donation.status', $donation->provider_order_id));
                }
            });
        });
    </script>

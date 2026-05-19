<div class="mx-auto w-full max-w-3xl rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        <h1 class="text-2xl font-semibold text-slate-900 sm:text-3xl">Status Donasi</h1>
        <p class="mt-2 text-sm text-slate-500">Ringkasan status pembayaran donasi kamu.</p>

        <div class="mt-6 grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 sm:p-5">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <span>Campaign</span>
                <span class="text-slate-900 sm:max-w-[65%] sm:text-right">{{ $donation->campaign?->title }}</span>
            </div>
            <div class="flex items-center justify-between gap-4">
                <span>Nominal</span>
                <span class="shrink-0 text-slate-900">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between gap-4">
                <span>Status</span>
                <span class="shrink-0 text-slate-900">{{ strtoupper($donation->status) }}</span>
            </div>
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <span>Order ID</span>
                <span class="font-mono text-xs text-slate-900 break-all sm:max-w-[65%] sm:text-right sm:text-sm">{{ $donation->provider_order_id }}</span>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <a href="{{ route('donation.campaigns.show', $donation->campaign) }}" class="w-full rounded-full border border-slate-300 px-4 py-2 text-center text-sm text-slate-700 hover:border-orange-400 sm:w-auto">
                Kembali ke Campaign
            </a>
            <a href="{{ route('donation.campaigns.index') }}" class="w-full rounded-full border border-slate-300 px-4 py-2 text-center text-sm text-slate-700 hover:border-orange-400 sm:w-auto">
                Lihat Campaign Lain
            </a>
        </div>
    </div>

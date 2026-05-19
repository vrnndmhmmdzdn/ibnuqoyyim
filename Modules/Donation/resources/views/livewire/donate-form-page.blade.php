<form wire:submit="submit" class="grid gap-6 lg:grid-cols-[2fr,1fr]">
        <section class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h1 class="text-2xl font-semibold text-slate-900">Donasi untuk {{ $campaign->title }}</h1>
                <p class="mt-2 text-sm text-slate-500">Pilih nominal cepat atau isi nominal sendiri.</p>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach ([10000, 25000, 50000, 100000] as $quick)
                        <button type="button" wire:click="setQuickAmount({{ $quick }})" class="quick-amount rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm text-slate-700 hover:border-orange-400">
                            Rp {{ number_format($quick, 0, ',', '.') }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-6">
                    <label class="text-sm text-slate-700">Nominal Donasi</label>
                    <input type="number" wire:model.defer="amount" min="1000" required class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:border-orange-400 focus:outline-none">
                    @error('amount')
                        <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-semibold text-slate-900">Data Donatur</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm text-slate-700">Nama</label>
                        <input type="text" wire:model.defer="donor_name" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:border-orange-400 focus:outline-none">
                        @error('donor_name')
                            <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm text-slate-700">Email (opsional)</label>
                        <input type="email" wire:model.defer="donor_email" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:border-orange-400 focus:outline-none">
                        @error('donor_email')
                            <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="text-sm text-slate-700">Doa / Ucapan (max 200)</label>
                    <textarea wire:model.defer="message" maxlength="200" rows="3" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:border-orange-400 focus:outline-none"></textarea>
                    @error('message')
                        <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
                <label class="flex items-center gap-3 text-sm text-slate-600">
                    <input type="checkbox" wire:model.defer="is_anonymous" class="h-4 w-4 rounded border-slate-300 bg-white">
                    Sembunyikan nama saya (Anonim)
                </label>
            </div>
        </section>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-lg font-semibold text-slate-900">Ringkasan</h3>
                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Target</span>
                        <span>Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Terkumpul</span>
                        <span>Rp {{ number_format($campaign->collected_amount_computed, 0, ',', '.') }}</span>
                    </div>
                </div>
                <button type="submit" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600">
                    Lanjut ke Pembayaran
                </button>
            </div>
            <a href="{{ route('donation.campaigns.show', $campaign) }}" class="text-sm text-slate-500 hover:text-orange-600">Kembali ke campaign</a>
        </aside>
    </form>

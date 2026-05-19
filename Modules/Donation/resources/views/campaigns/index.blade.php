<div class="space-y-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Campaign Donasi</p>
                    <h1 class="mt-3 text-2xl font-semibold text-slate-900 md:text-3xl">Bantu yang membutuhkan lewat campaign pilihanmu</h1>
                    <p class="mt-3 max-w-2xl text-sm text-slate-600">
                        Pilih campaign aktif, lihat progres pengumpulan, dan donasikan dengan cepat.
                    </p>
                </div>
                <div class="rounded-2xl border border-orange-200 bg-orange-50 px-6 py-5 text-sm text-orange-700">
                    <p class="text-xs uppercase tracking-widest text-orange-500">Total Campaign</p>
                    <p class="mt-2 text-3xl font-semibold text-orange-600">{{ $campaigns->total() }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($campaigns as $campaign)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    @if($campaign->cover_image_path)
                        <div class="h-40 w-full overflow-hidden rounded-xl bg-slate-200">
                            <img src="{{ asset('storage/' . $campaign->cover_image_path) }}" alt="{{ $campaign->title }}" class="h-full w-full object-cover">
                        </div>
                    @else
                        <div class="h-40 w-full rounded-xl bg-gradient-to-br from-orange-100 via-orange-50 to-slate-100"></div>
                    @endif
                    <div class="mt-5 space-y-4">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">{{ $campaign->title }}</h2>
                            <p class="mt-2 text-sm text-slate-600">{{ $campaign->description }}</p>
                        </div>
                        <div>
                            @php($collected = $campaign->paid_total ?? $campaign->collected_amount_computed)
                            @php($rawProgress = $campaign->target_amount > 0 ? (($collected / $campaign->target_amount) * 100) : 0)
                            @php($progress = $collected > 0 && $rawProgress < 1 ? 1 : min(100, (int) round($rawProgress)))
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span>Terkumpul</span>
                                <span>{{ number_format($collected, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-orange-500" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                <span>Target {{ number_format($campaign->target_amount, 0, ',', '.') }}</span>
                                <span>{{ $progress }}%</span>
                            </div>
                        </div>
                        <a href="{{ route('donation.campaigns.show', $campaign) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600">
                            Lihat Campaign
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-500">
                    Belum ada campaign aktif.
                </div>
            @endforelse
        </section>

        <div>{{ $campaigns->links() }}</div>
    </div>

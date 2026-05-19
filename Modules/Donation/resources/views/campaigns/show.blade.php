<div class="grid gap-6 lg:grid-cols-[2fr,1fr]">
        <section class="space-y-6">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                @if($campaign->cover_image_path)
                    <div class="h-72 w-full bg-slate-200">
                        <img src="{{ asset('storage/' . $campaign->cover_image_path) }}" alt="{{ $campaign->title }}" class="h-full w-full object-cover">
                    </div>
                @else
                    <div class="h-72 w-full bg-gradient-to-br from-orange-100 to-slate-100"></div>
                @endif
                <div class="p-6">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Campaign</p>
                    <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $campaign->title }}</h1>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $campaign->description }}</p>
                    <div class="mt-6 flex items-center gap-6 border-t border-slate-100 pt-4 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-2 w-2 rounded-full bg-orange-500"></span>
                            <span>{{ $stats['donors'] }} donasi</span>
                        </div>
                        @if($campaign->deadline_at)
                            <div>Berakhir {{ $campaign->deadline_at->locale('id')->diffForHumans() }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between text-sm text-slate-600">
                    <span>Terkumpul</span>
                    <span class="text-slate-900">Rp {{ number_format($campaign->collected_amount_computed, 0, ',', '.') }}</span>
                </div>
                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-orange-500" style="width: {{ $campaign->progress_percent }}%"></div>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Target Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</span>
                    <span>{{ $campaign->progress_percent }}%</span>
                </div>
            </div>

            @if($campaign->contact_name || $campaign->contact_phone)
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="text-sm font-semibold text-slate-900">Contact Person</h3>
                    <div class="mt-3 space-y-2 text-sm text-slate-600">
                        @if($campaign->contact_name)
                            <div class="flex items-center justify-between">
                                <span>Nama</span>
                                <span class="text-slate-900">{{ $campaign->contact_name }}</span>
                            </div>
                        @endif
                        @if($campaign->contact_phone)
                            <div class="flex items-center justify-between">
                                <span>Kontak</span>
                                <span class="text-slate-900">{{ $campaign->contact_phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <p class="text-xs text-slate-500">Donasi Sekarang</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">
                    Rp {{ number_format($campaign->collected_amount_computed, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-500">dari Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</p>
                <a href="{{ route('donation.donate.create', $campaign) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white">
                    Donasi Sekarang
                </a>
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-slate-500">
                    <div class="rounded-lg border border-slate-200 px-3 py-2 text-center">{{ $stats['donors'] }} Donatur</div>
                    <div class="rounded-lg border border-slate-200 px-3 py-2 text-center">{{ $campaign->progress_percent }}% Tercapai</div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-sm font-semibold text-slate-900">Doa & Dukungan Terbaru</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($donations as $donation)
                        <div class="rounded-xl border border-slate-100 p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">
                                        {{ $donation->is_anonymous ? 'Anonim' : ($donation->donor_name ?: 'Hamba Allah') }}
                                    </p>
                                    <p class="text-[11px] text-slate-400">{{ $donation->paid_at?->locale('id')->diffForHumans() }}</p>
                                </div>
                                <p class="text-xs font-semibold text-slate-900">
                                    Rp {{ number_format($donation->amount, 0, ',', '.') }}
                                </p>
                            </div>
                            @if($donation->message)
                                <p class="mt-2 text-xs text-slate-600">"{{ $donation->message }}"</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada donasi masuk.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>

<x-filament-panels::page>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    @endpush
    <div class="space-y-5">

        {{-- Tidak ada data guru --}}
        @if (!$this->guru)
            <div
                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
                <p class="text-sm text-red-600 dark:text-red-400 font-medium">
                    Akun kamu belum terhubung ke data guru. Hubungi admin.
                </p>
            </div>
        @else
            {{-- Filter --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex flex-wrap items-end gap-4">

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bulan</label>
                        <select wire:model.live="bulan"
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                            @foreach ($this->bulanList as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tahun</label>
                        <select wire:model.live="tahun"
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                            @foreach ($this->tahunList as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                        <select wire:model.live="status"
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Semua Status</option>
                            @foreach (\Modules\AbsensiStaf\Models\AbsensiStaf::STATUS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- Rekap Bulan --}}
            @php $rekap = $this->rekapBulan; @endphp
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach ([['label' => 'Hadir', 'value' => $rekap['hadir'], 'color' => 'green'], ['label' => 'Terlambat', 'value' => $rekap['terlambat'], 'color' => 'amber'], ['label' => 'Izin', 'value' => $rekap['izin'], 'color' => 'blue'], ['label' => 'Sakit', 'value' => $rekap['sakit'], 'color' => 'purple'], ['label' => 'Alpha', 'value' => $rekap['alpha'], 'color' => 'red'], ['label' => 'Total Hari', 'value' => $rekap['total_hari'], 'color' => 'gray']] as $stat)
                    @php
                        $c = match ($stat['color']) {
                            'green' => [
                                'border' => 'border-l-green-500',
                                'val' => 'text-green-700 dark:text-green-300',
                            ],
                            'amber' => [
                                'border' => 'border-l-amber-500',
                                'val' => 'text-amber-700 dark:text-amber-300',
                            ],
                            'blue' => ['border' => 'border-l-blue-500', 'val' => 'text-blue-700 dark:text-blue-300'],
                            'purple' => [
                                'border' => 'border-l-purple-500',
                                'val' => 'text-purple-700 dark:text-purple-300',
                            ],
                            'red' => ['border' => 'border-l-red-500', 'val' => 'text-red-700 dark:text-red-300'],
                            default => ['border' => 'border-l-gray-400', 'val' => 'text-gray-700 dark:text-gray-300'],
                        };
                    @endphp
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 border-l-4 {{ $c['border'] }} p-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold {{ $c['val'] }}">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Total Durasi --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Jam Kerja Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-0.5">{{ $rekap['total_durasi'] }}</p>
                </div>
                <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl">
                    <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- Tabel Riwayat --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">
                        Riwayat {{ $this->bulanList[$bulan] }} {{ $tahun }}
                    </h3>
                </div>

                @if ($this->riwayat->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada data absensi</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach ($this->riwayat as $abs)
                            @php
                                $badgeColor = match ($abs->status) {
                                    'hadir' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    'terlambat'
                                        => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                    'izin' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                    'sakit'
                                        => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                    'alpha' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                };
                                $punyaLokasi = $abs->clock_in_lat && $abs->clock_in_lng;
                            @endphp
                            <div class="px-5 py-4 border-b border-gray-50 dark:border-gray-700">
                                <div class="flex items-center gap-4">

                                    {{-- Tanggal --}}
                                    <div class="flex-shrink-0 text-center w-12">
                                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">
                                            {{ $abs->tanggal->locale('id')->translatedFormat('M') }}
                                        </p>
                                        <p class="text-xl font-bold text-gray-800 dark:text-white">
                                            {{ $abs->tanggal->format('d') }}
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ $abs->tanggal->locale('id')->translatedFormat('D') }}
                                        </p>
                                    </div>

                                    <div class="w-px self-stretch bg-gray-200 dark:bg-gray-600 flex-shrink-0"></div>

                                    {{-- Detail --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            @if ($abs->clock_in_at)
                                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                                    Masuk: <span
                                                        class="font-semibold text-gray-800 dark:text-white">{{ $abs->clock_in_at->format('H:i') }}</span>
                                                </span>
                                            @endif
                                            @if ($abs->clock_out_at)
                                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                                    Pulang: <span
                                                        class="font-semibold text-gray-800 dark:text-white">{{ $abs->clock_out_at->format('H:i') }}</span>
                                                </span>
                                            @endif
                                            @if ($abs->durasi)
                                                <span
                                                    class="text-xs text-gray-400 dark:text-gray-500">{{ $abs->durasi }}</span>
                                            @endif
                                        </div>
                                        @if ($abs->telat > 0)
                                            <p class="text-xs text-red-500 mt-0.5">Terlambat {{ $abs->telat }} menit
                                            </p>
                                        @endif
                                        @if ($abs->keterangan)
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                                {{ $abs->keterangan }}</p>
                                        @endif

                                        {{-- Tombol lihat lokasi --}}
                                        @if ($punyaLokasi)
                                            <button
                                                onclick="togglePeta(
                    {{ $abs->id }},
                    {{ $abs->clock_in_lat }}, {{ $abs->clock_in_lng }},
                    {{ $abs->clock_out_lat ?? 'null' }}, {{ $abs->clock_out_lng ?? 'null' }},
                    '{{ $abs->clock_in_at?->format('H:i') }}',
                    '{{ $abs->clock_out_at?->format('H:i') ?? '' }}'
                )"
                                                class="mt-2 inline-flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400 hover:underline">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span id="label-peta-{{ $abs->id }}">Lihat Lokasi</span>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Badge --}}
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium flex-shrink-0 {{ $badgeColor }}">
                                        {{ \Modules\AbsensiStaf\Models\AbsensiStaf::STATUS[$abs->status] ?? '-' }}
                                    </span>

                                </div>

                                {{-- Panel peta per baris --}}
                                @if ($punyaLokasi)
                                    <div id="peta-{{ $abs->id }}" class="hidden mt-3 rounded-xl overflow-hidden"
                                        style="height: 220px;">
                                        <div id="map-{{ $abs->id }}" style="height: 100%; width: 100%;"></div>
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        @endif
    </div>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
        <script>
            const petaMaps = {};

            function togglePeta(id, latIn, lngIn, latOut, lngOut, jamIn, jamOut) {
                const panel = document.getElementById('peta-' + id);
                const label = document.getElementById('label-peta-' + id);
                if (!panel) return;

                const sedangTampil = !panel.classList.contains('hidden');

                if (sedangTampil) {
                    panel.classList.add('hidden');
                    label.textContent = 'Lihat Lokasi';
                    return;
                }

                panel.classList.remove('hidden');
                label.textContent = 'Tutup Peta';

                // Sudah pernah diinit
                if (petaMaps[id]) {
                    setTimeout(() => petaMaps[id].invalidateSize(), 100);
                    return;
                }

                const map = L.map('map-' + id).setView([latIn, lngIn], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                // Marker clock in — hijau
                L.circleMarker([latIn, lngIn], {
                    radius: 10,
                    fillColor: '#22c55e',
                    color: '#fff',
                    weight: 2,
                    fillOpacity: 1,
                }).addTo(map).bindPopup(`<b>Clock In</b><br>${jamIn}`);

                // Marker clock out — biru (kalau ada)
                if (latOut && lngOut) {
                    L.circleMarker([latOut, lngOut], {
                        radius: 10,
                        fillColor: '#3b82f6',
                        color: '#fff',
                        weight: 2,
                        fillOpacity: 1,
                    }).addTo(map).bindPopup(`<b>Clock Out</b><br>${jamOut}`);

                    // Garis penghubung
                    L.polyline([
                        [latIn, lngIn],
                        [latOut, lngOut]
                    ], {
                        color: '#94a3b8',
                        dashArray: '5,5',
                        weight: 1.5
                    }).addTo(map);

                    // Fit bounds
                    map.fitBounds([
                        [latIn, lngIn],
                        [latOut, lngOut]
                    ], {
                        padding: [30, 30]
                    });
                }

                petaMaps[id] = map;
                setTimeout(() => map.invalidateSize(), 100);
            }
        </script>
    @endpush
</x-filament-panels::page>

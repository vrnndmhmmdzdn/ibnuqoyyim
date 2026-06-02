<x-filament-panels::page>
<div class="space-y-6">

    {{-- Filter Tanggal --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal:</label>
                <input type="date" wire:model.live="tanggal_filter"
                    max="{{ today()->format('Y-m-d') }}"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
            </div>

            {{-- Tombol cepat --}}
            <div class="flex gap-2">
                <button wire:click="$set('tanggal_filter', '{{ today()->format('Y-m-d') }}')"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors
                        {{ $tanggal_filter === today()->format('Y-m-d')
                            ? 'bg-primary-600 text-white border-primary-600'
                            : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    Hari Ini
                </button>
                <button wire:click="$set('tanggal_filter', '{{ today()->subDay()->format('Y-m-d') }}')"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Kemarin
                </button>
            </div>

            @if($this->isLibur)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                Hari Libur
            </span>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    @php $rekap = $this->rekap; @endphp
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @foreach([
            ['label' => 'Hadir',       'value' => $rekap['hadir'],      'color' => 'green',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Terlambat',   'value' => $rekap['terlambat'],  'color' => 'amber',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Izin',        'value' => $rekap['izin'],       'color' => 'blue',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Sakit',       'value' => $rekap['sakit'],      'color' => 'purple', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ['label' => 'Alpha',       'value' => $rekap['alpha'],      'color' => 'red',    'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Belum Absen', 'value' => $rekap['belumAbsen'], 'color' => 'gray',   'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $stat)
        @php
            $c = match($stat['color']) {
                'green'  => ['border' => 'border-l-green-500',  'bg' => 'bg-green-50 dark:bg-green-900/20',   'text' => 'text-green-600 dark:text-green-400',   'val' => 'text-green-700 dark:text-green-300'],
                'amber'  => ['border' => 'border-l-amber-500',  'bg' => 'bg-amber-50 dark:bg-amber-900/20',   'text' => 'text-amber-600 dark:text-amber-400',   'val' => 'text-amber-700 dark:text-amber-300'],
                'blue'   => ['border' => 'border-l-blue-500',   'bg' => 'bg-blue-50 dark:bg-blue-900/20',     'text' => 'text-blue-600 dark:text-blue-400',     'val' => 'text-blue-700 dark:text-blue-300'],
                'purple' => ['border' => 'border-l-purple-500', 'bg' => 'bg-purple-50 dark:bg-purple-900/20', 'text' => 'text-purple-600 dark:text-purple-400', 'val' => 'text-purple-700 dark:text-purple-300'],
                'red'    => ['border' => 'border-l-red-500',    'bg' => 'bg-red-50 dark:bg-red-900/20',       'text' => 'text-red-600 dark:text-red-400',       'val' => 'text-red-700 dark:text-red-300'],
                default  => ['border' => 'border-l-gray-400',   'bg' => 'bg-gray-50 dark:bg-gray-700/50',     'text' => 'text-gray-500 dark:text-gray-400',     'val' => 'text-gray-700 dark:text-gray-300'],
            };
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 border-l-4 {{ $c['border'] }} p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                <div class="p-1.5 {{ $c['bg'] }} rounded-lg">
                    <svg class="w-4 h-4 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold {{ $c['val'] }}">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">dari {{ $this->totalStaf }} staf</p>
        </div>
        @endforeach
    </div>

    {{-- Grafik Mingguan --}}
    @php $grafik = $this->grafikMingguan; @endphp
    @if(!empty($grafik))
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4">Kehadiran 7 Hari Terakhir</h3>
        <div class="flex items-end gap-2 h-32">
            @foreach($grafik as $item)
            @php
                $pct = $this->totalStaf > 0
                    ? round(($item['hadir'] / $this->totalStaf) * 100)
                    : 0;
                $h = max(4, round(($pct / 100) * 128));
            @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $pct }}%</span>
                <div class="w-full rounded-t-lg bg-primary-500 dark:bg-primary-600 transition-all"
                    style="height: {{ $h }}px"></div>
                <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $item['hari'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-full bg-primary-500"></div>
                <span class="text-xs text-gray-500 dark:text-gray-400">Hadir/Terlambat</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Daftar Staf --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 dark:text-white">Detail Kehadiran Staf</h3>
            <span class="text-xs text-gray-400">
                {{ $this->tanggalDipilih->locale('id')->translatedFormat('l, d F Y') }}
            </span>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-700">
            @foreach($this->daftarStaf as $item)
            @php
                $badgeColor = match($item->status) {
                    'hadir'     => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                    'terlambat' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                    'izin'      => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                    'sakit'     => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                    'alpha'     => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                    default     => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                };
                $statusLabel = match($item->status) {
                    'hadir'     => 'Hadir',
                    'terlambat' => 'Terlambat',
                    'izin'      => 'Izin',
                    'sakit'     => 'Sakit',
                    'alpha'     => 'Alpha',
                    default     => 'Belum Absen',
                };
            @endphp
            <div class="px-5 py-3 flex items-center gap-4">

                {{-- Avatar --}}
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0
                    {{ $item->status === 'belum' || $item->status === 'alpha'
                        ? 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'
                        : 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' }}">
                    {{ strtoupper(substr($item->guru->name, 0, 1)) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                        {{ $item->guru->name }}
                    </p>
                    <div class="flex items-center gap-3 mt-0.5">
                        @if($item->clock_in !== '-')
                        <span class="text-xs text-gray-400">
                            Masuk: <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $item->clock_in }}</span>
                        </span>
                        @endif
                        @if($item->clock_out !== '-')
                        <span class="text-xs text-gray-400">
                            Pulang: <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $item->clock_out }}</span>
                        </span>
                        @endif
                        @if($item->durasi !== '-' && $item->clock_out !== '-')
                        <span class="text-xs text-gray-400">
                            {{ $item->durasi }}
                        </span>
                        @endif
                        @if($item->telat > 0)
                        <span class="text-xs text-red-500">
                            Terlambat {{ $item->telat }} menit
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Badge Status --}}
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium flex-shrink-0 {{ $badgeColor }}">
                    {{ $statusLabel }}
                </span>

            </div>
            @endforeach
        </div>
    </div>

</div>
</x-filament-panels::page>
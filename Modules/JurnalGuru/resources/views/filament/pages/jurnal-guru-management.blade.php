<x-filament-panels::page>
<div class="space-y-6">

    {{-- Stats --}}
    @php
        $today = \Carbon\Carbon::today();
        $totalGuru = \Modules\Guru\Models\Guru::count();

        $guruSudahIsi = \Modules\JurnalGuru\Models\JurnalGuru::whereDate('tanggal', $today)
            ->distinct('guru_id')->count('guru_id');

        $guroBelumIsi = $totalGuru - $guruSudahIsi;

        $jurnalHariIni = \Modules\JurnalGuru\Models\JurnalGuru::whereDate('tanggal', $today)->count();

        $jurnalBulanIni = \Modules\JurnalGuru\Models\JurnalGuru::whereMonth('tanggal', $today->month)
            ->whereYear('tanggal', $today->year)->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Jurnal Hari Ini',      'value' => $jurnalHariIni,  'color' => 'blue',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Guru Sudah Isi',        'value' => $guruSudahIsi,   'color' => 'green', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Guru Belum Isi',         'value' => $guroBelumIsi,   'color' => 'red',   'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Jurnal Bulan Ini',       'value' => $jurnalBulanIni, 'color' => 'amber', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ] as $stat)
        @php
            $colors = [
                'blue'  => ['border' => 'border-l-blue-500',  'bg' => 'bg-blue-50 dark:bg-blue-900/20',  'icon' => 'text-blue-500'],
                'green' => ['border' => 'border-l-green-500', 'bg' => 'bg-green-50 dark:bg-green-900/20', 'icon' => 'text-green-500'],
                'red'   => ['border' => 'border-l-red-500',   'bg' => 'bg-red-50 dark:bg-red-900/20',   'icon' => 'text-red-500'],
                'amber' => ['border' => 'border-l-amber-500', 'bg' => 'bg-amber-50 dark:bg-amber-900/20', 'icon' => 'text-amber-500'],
            ][$stat['color']];
        @endphp
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 border-l-4 {{ $colors['border'] }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stat['value'] }}</p>
                </div>
                <div class="p-3 {{ $colors['bg'] }} rounded-full">
                    <svg class="w-6 h-6 {{ $colors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Status guru hari ini --}}
    @php
        $semuaGuru = \Modules\Guru\Models\Guru::all();
        $guruSudahIds = \Modules\JurnalGuru\Models\JurnalGuru::whereDate('tanggal', $today)
            ->pluck('guru_id')->unique()->toArray();
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Status Pengisian Jurnal Hari Ini</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $today->locale('id')->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-700">
            @foreach($semuaGuru as $guru)
            @php $sudah = in_array($guru->id, $guruSudahIds); @endphp
            <div class="px-6 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $sudah ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                        {{ strtoupper(substr($guru->name, 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $guru->name }}</span>
                </div>
                @if($sudah)
                    @php
                        $jumlahJurnal = \Modules\JurnalGuru\Models\JurnalGuru::where('guru_id', $guru->id)
                            ->whereDate('tanggal', $today)->count();
                    @endphp
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 dark:text-green-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Sudah isi {{ $jumlahJurnal }} jurnal
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-500 dark:text-red-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Belum isi
                    </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Jurnal terbaru --}}
    @php
        $jurnalTerbaru = \Modules\JurnalGuru\Models\JurnalGuru::with(['guru', 'kelas', 'mataPelajaran'])
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Jurnal Terbaru</h3>
        </div>
        <div class="divide-y divide-gray-50 dark:divide-gray-700">
            @forelse($jurnalTerbaru as $jurnal)
            @php
                $capaiColor = match($jurnal->capaian) {
                    'tercapai' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                    'sebagian' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                    'belum'    => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                    default    => 'bg-gray-100 text-gray-700',
                };
                $statusColor = $jurnal->status === 'submitted'
                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                    : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';
            @endphp
            <div class="px-6 py-4 flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-sm text-gray-800 dark:text-white">
                            {{ $jurnal->guru?->name ?? '-' }}
                        </p>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $jurnal->kelas?->nama_kelas ?? '-' }}
                        </p>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $jurnal->mataPelajaran?->pelajaran ?? '-' }}
                        </p>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        {{ $jurnal->tanggal->locale('id')->translatedFormat('l, d F Y') }}
                        · {{ substr($jurnal->jam_mulai, 0, 5) }}–{{ substr($jurnal->jam_selesai, 0, 5) }}
                        · {{ $jurnal->materi }}
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $capaiColor }}">
                        {{ \Modules\JurnalGuru\Models\JurnalGuru::CAPAIAN[$jurnal->capaian] ?? '-' }}
                    </span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                        {{ \Modules\JurnalGuru\Models\JurnalGuru::STATUS[$jurnal->status] ?? '-' }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">
                Belum ada jurnal yang diisi.
            </div>
            @endforelse
        </div>
    </div>

</div>
</x-filament-panels::page>
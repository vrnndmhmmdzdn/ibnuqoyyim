<x-filament-panels::page>
    @include('kalender-didik::components.styles')

    <div class="space-y-6">

        {{-- Stats --}}
        @php
            $today = \Carbon\Carbon::today();

            $agendaHariIni = \Modules\KalenderDidik\Models\Kaldik::where('jam_mulai', '<=', $today->copy()->endOfDay())
                ->where('jam_selesai', '>=', $today->copy()->startOfDay())
                ->count();

            $agendaPekanIni = \Modules\KalenderDidik\Models\Kaldik::where('jam_mulai', '<=', $today->copy()->endOfWeek())
                ->where('jam_selesai', '>=', $today->copy()->startOfWeek())
                ->count();

            $agendaBulanIni = \Modules\KalenderDidik\Models\Kaldik::where('jam_mulai', '<=', $today->copy()->endOfMonth())
                ->where('jam_selesai', '>=', $today->copy()->startOfMonth())
                ->count();

            $totalKaldiks = \Modules\KalenderDidik\Models\Kaldik::count();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Agenda Hari Ini',  'value' => $agendaHariIni,  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['label' => 'Agenda Pekan Ini', 'value' => $agendaPekanIni, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Agenda Bulan Ini', 'value' => $agendaBulanIni, 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['label' => 'Total Kegiatan',   'value' => $totalKaldiks,   'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ] as $stat)
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 border-l-4 border-l-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stat['value'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-full">
                        <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Agenda yang akan datang --}}
        @php
            $mendatang = \Modules\KalenderDidik\Models\Kaldik::where('jam_selesai', '>=', now())
                ->orderBy('jam_mulai')
                ->limit(10)
                ->get();

            $warnaKategori = [
                'Ujian'        => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                'Libur'        => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'Akademik'     => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                'Non-Akademik' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            ];
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 dark:text-white">Agenda yang Akan Datang</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">10 kegiatan terdekat</span>
            </div>

            @if($mendatang->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada agenda yang akan datang</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Tambahkan kegiatan menggunakan tombol di atas</p>
                </div>
            @else
                <div class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($mendatang as $kegiatan)
                    @php
                        $now = \Carbon\Carbon::now();
                        $isOngoing = $now->between($kegiatan->jam_mulai, $kegiatan->jam_selesai);
                        $isToday   = $kegiatan->jam_mulai->isToday() || $isOngoing;
                    @endphp
                    <div class="px-6 py-4 flex items-start gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">

                        {{-- Tanggal --}}
                        <div class="flex-shrink-0 text-center w-12">
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">
                                {{ $kegiatan->jam_mulai->locale('id')->translatedFormat('M') }}
                            </p>
                            <p class="text-xl font-bold {{ $isToday ? 'text-blue-600 dark:text-blue-400' : 'text-gray-800 dark:text-white' }}">
                                {{ $kegiatan->jam_mulai->format('d') }}
                            </p>
                        </div>

                        {{-- Garis pembatas --}}
                        <div class="w-px self-stretch bg-gray-200 dark:bg-gray-600 flex-shrink-0"></div>

                        {{-- Konten --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        {{ $kegiatan->nama_acara }}
                                        @if($isOngoing)
                                            <span class="ml-2 inline-flex items-center gap-1 text-xs font-medium text-green-700 dark:text-green-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse inline-block"></span>
                                                Berlangsung
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $kegiatan->kegiatan }}</p>
                                </div>
                                <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $warnaKategori[$kegiatan->kategori] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $kegiatan->kategori }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-400 dark:text-gray-500">
                                <span>
                                    {{ $kegiatan->jam_mulai->locale('id')->translatedFormat('d M Y') }}
                                    @if(!$kegiatan->jam_mulai->isSameDay($kegiatan->jam_selesai))
                                        — {{ $kegiatan->jam_selesai->locale('id')->translatedFormat('d M Y') }}
                                    @endif
                                </span>
                                <span>•</span>
                                <span>{{ $kegiatan->subject }}</span>
                                <span>•</span>
                                <span>{{ $kegiatan->tahun_ajaran }}</span>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>
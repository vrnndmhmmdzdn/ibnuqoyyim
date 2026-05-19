<x-filament-panels::page>
    @include('jadwal-pelajaran::components.styles')
    <div class="space-y-6">
        <!-- Header Info -->
        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-2">Selamat Datang</h2>
            <p class="text-gray-600 dark:text-gray-300">
                Gunakan sistem ini untuk membuat jadwal pelajaran lebih mudah. 
                Pilih tanggal, lihat ketersediaan waktu, dan buat jadwal dengan mudah.
            </p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $today = \Carbon\Carbon::today();
                $todayJadwalPelajarans = \Modules\JadwalPelajaran\Models\JadwalPelajaran::whereDate('jam_mulai', $today)->count();
                $tomorrowJadwalPelajarans = \Modules\JadwalPelajaran\Models\JadwalPelajaran::whereDate('jam_mulai', $today->copy()->addDay())->count();
                $thisWeekJadwalPelajarans = \Modules\JadwalPelajaran\Models\JadwalPelajaran::whereBetween('jam_mulai', [
                    $today->copy()->startOfWeek(),
                    $today->copy()->endOfWeek()
                ])->count();
                $totalJadwalPelajarans = \Modules\JadwalPelajaran\Models\JadwalPelajaran::count();
            @endphp
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border-l-4 border-orange-500 dark:border-orange-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Jadwal Pelajaran Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayJadwalPelajarans }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border-l-4 border-orange-500 dark:border-orange-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Jadwal Pelajaran Besok</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tomorrowJadwalPelajarans }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border-l-4 border-orange-500 dark:border-orange-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Minggu Ini</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $thisWeekJadwalPelajarans }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow border-l-4 border-orange-500 dark:border-orange-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Jadwal Pelajaran</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalJadwalPelajarans }}</p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main JadwalPelajaran Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            @livewire('modules.jadwal-pelajaran.filament.pages.jadwal-pelajaran-form')
        </div>

        <!-- Recent JadwalPelajarans -->
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Jadwal Pelajaran Terbaru Hari Ini</h3>
        @php
            $recentJadwalPelajarans = \Modules\JadwalPelajaran\Models\JadwalPelajaran::whereDate('jam_mulai', $today)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        @endphp
        
        @if($recentJadwalPelajarans->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pelajaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Guru</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hari</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentJadwalPelajarans as $jadwal)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $jadwal->mataPelajaran->pelajaran}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $jadwal->guru->name}}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $jadwal->jadwal_kelas === 'Lap A' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                    {{ $jadwal->jadwal_kelas === 'Lap B' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                                    {{ $jadwal->jadwal_kelas === 'Lap C' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                    {{ $jadwal->jadwal_kelas === 'Lap D' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                ">
                                    {{ $jadwal->jadwal_kelas }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $jadwal->time_range }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $jadwal->hari }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $status = 'upcoming';
                                    if ($now->gt($jadwal->jam_selesai)) {
                                        $status = 'completed';
                                    } elseif ($now->between($jadwal->jam_mulai, $jadwal->jam_selesai)) {
                                        $status = 'ongoing';
                                    }
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $status === 'completed' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}
                                    {{ $status === 'ongoing' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                    {{ $status === 'upcoming' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                                ">
                                    {{ $status === 'completed' ? 'Selesai' : '' }}
                                    {{ $status === 'ongoing' ? 'Berlangsung' : '' }}
                                    {{ $status === 'upcoming' ? 'Akan Datang' : '' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">Belum ada jadwal hari ini</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai buat jadwal untuk customer menggunakan form di atas.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
<x-filament-panels::page>
    <div class="space-y-4">

        {{-- Filter Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap items-end gap-4">

                <div class="flex-1 min-w-40">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Guru</label>
                    <select wire:model.live="guru_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Semua Guru</option>
                        @foreach($this->guruList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-40">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas</label>
                    <select wire:model.live="kelas_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($this->kelasList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-40">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capaian</label>
                    <select wire:model.live="capaian"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Semua Capaian</option>
                        @foreach($this->capaianList as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Legend --}}
                <div class="flex items-center gap-2 ml-auto">
                    <span class="text-xs text-gray-400">Capaian:</span>
                    <span class="inline-flex items-center gap-1 text-xs">
                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                        Tercapai
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs">
                        <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                        Sebagian
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs">
                        <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                        Belum
                    </span>
                </div>

            </div>
        </div>

        {{-- Kalender --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div id="jurnal-calendar"></div>
        </div>

        {{-- Detail Panel --}}
        <div id="jurnal-detail" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 id="detail-title" class="font-semibold text-gray-800 dark:text-white"></h3>
                    <button onclick="document.getElementById('jurnal-detail').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="detail-content" class="p-6"></div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const calendarEl  = document.getElementById('jurnal-calendar');
            const detailPanel = document.getElementById('jurnal-detail');
            const detailTitle = document.getElementById('detail-title');
            const detailContent = document.getElementById('detail-content');

            let jurnalsData = @js($this->jurnals);
            let calendar;

            function initCalendar(data) {
                if (calendar) calendar.destroy();

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay',
                    },
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week:  'Minggu',
                        day:   'Hari',
                    },
                    height: 'auto',
                    dayMaxEvents: 3,
                    events: data,
                    eventClick: function (info) {
                        showDetail(info.event);
                    },
                });

                calendar.render();
            }

            function showDetail(event) {
                const p = event.extendedProps;

                // Warna badge capaian
                const badgeColor = {
                    'tercapai': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                    'sebagian': 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                    'belum':    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                }[p.capaian_raw] ?? 'bg-gray-100 text-gray-700';

                const statusColor = p.status === 'Submitted'
                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                    : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';

                detailTitle.textContent = `${p.mata_pelajaran} — ${p.kelas} — ${p.tanggal}`;

                detailContent.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Kolom kiri --}}
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-2">Informasi Mengajar</p>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Guru</span>
                                        <span class="font-medium text-gray-800 dark:text-white">${p.guru}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Kelas</span>
                                        <span class="font-medium text-gray-800 dark:text-white">${p.kelas}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Mata Pelajaran</span>
                                        <span class="font-medium text-gray-800 dark:text-white">${p.mata_pelajaran}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Tanggal</span>
                                        <span class="font-medium text-gray-800 dark:text-white">${p.tanggal}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Jam</span>
                                        <span class="font-medium text-gray-800 dark:text-white">${p.jam_mulai} – ${p.jam_selesai}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Pertemuan Ke</span>
                                        <span class="font-medium text-gray-800 dark:text-white">${p.pertemuan_ke}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-2">Kehadiran</p>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Hadir</span>
                                        <span class="font-medium text-green-600 dark:text-green-400">${p.jumlah_hadir} siswa</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Tidak Hadir</span>
                                        <span class="font-medium text-red-500 dark:text-red-400">${p.jumlah_tidak_hadir} siswa</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 dark:text-gray-400">Total</span>
                                        <span class="font-medium text-gray-800 dark:text-white">${p.total_siswa} siswa (${p.persentase_hadir})</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-2">Status</p>
                                <div class="flex gap-2 flex-wrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium ${badgeColor}">
                                        ${p.capaian}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium ${statusColor}">
                                        ${p.status}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom kanan --}}
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Materi</p>
                                <p class="text-gray-800 dark:text-white font-medium">${p.materi}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Kompetensi Dasar</p>
                                <p class="text-gray-700 dark:text-gray-300">${p.kompetensi_dasar}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Deskripsi Kegiatan</p>
                                <p class="text-gray-700 dark:text-gray-300">${p.deskripsi_kegiatan}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Metode & Media</p>
                                <p class="text-gray-700 dark:text-gray-300">${p.metode} — ${p.media}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Tindak Lanjut</p>
                                <p class="text-gray-700 dark:text-gray-300">${p.tindak_lanjut}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Catatan</p>
                                <p class="text-gray-700 dark:text-gray-300">${p.catatan}</p>
                            </div>
                        </div>

                    </div>
                `;

                detailPanel.classList.remove('hidden');
                detailPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            // Init pertama
            initCalendar(jurnalsData);

            // Re-init kalau Livewire update filter
            Livewire.on('jurnals-updated', (data) => {
                jurnalsData = data;
                initCalendar(jurnalsData);
            });

            // Custom CSS
            const style = document.createElement('style');
            style.textContent = `
                .fc-theme-standard .fc-scrollgrid { border: none; }
                .fc-theme-standard td, .fc-theme-standard th { border-color: #f3f4f6; }
                .dark .fc-theme-standard td, .dark .fc-theme-standard th { border-color: #374151; }
                .fc-col-header-cell { background: #f9fafb; padding: 10px 8px; }
                .dark .fc-col-header-cell { background: #1f2937; color: #d1d5db; }
                .fc-daygrid-day { background: #ffffff; min-height: 80px; }
                .dark .fc-daygrid-day { background: #111827; }
                .fc-daygrid-day.fc-day-today { background: #f0fdf4 !important; }
                .dark .fc-daygrid-day.fc-day-today { background: #14532d !important; }
                .fc-daygrid-day-number { color: #374151; font-weight: 500; padding: 6px; }
                .dark .fc-daygrid-day-number { color: #d1d5db; }
                .fc-button-primary { background: #22c55e !important; border-color: #16a34a !important; border-radius: 8px !important; }
                .fc-button-primary:hover { background: #16a34a !important; }
                .fc-button-primary.fc-button-active { background: #15803d !important; }
                .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700 !important; color: #1f2937; }
                .dark .fc-toolbar-title { color: #f9fafb; }
                .fc-event { border-radius: 6px !important; border: none !important; font-size: 11px !important; font-weight: 600 !important; padding: 1px 4px !important; cursor: pointer; }
                .fc-event:hover { opacity: 0.85; transform: translateY(-1px); }
            `;
            document.head.appendChild(style);
        });
    </script>
    @endpush

</x-filament-panels::page>
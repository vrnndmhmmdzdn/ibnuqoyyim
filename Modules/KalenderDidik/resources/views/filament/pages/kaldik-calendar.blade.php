<x-filament-panels::page>
    @include('kalender-didik::components.styles')

    <div>
        <x-filament::section>
            <div class="space-y-8">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Kalender Pendidikan</h2>
                    <div class="flex flex-wrap items-center gap-2 md:gap-3 w-full md:w-auto">
                        <button id="prev-month" class="calendar-nav-button w-full md:w-auto">← Back</button>
                        <select id="month-selector" class="calendar-selector w-full md:w-40">
                            <option value="0">Januari</option>
                            <option value="1">Februari</option>
                            <option value="2">Maret</option>
                            <option value="3">April</option>
                            <option value="4">Mei</option>
                            <option value="5">Juni</option>
                            <option value="6">Juli</option>
                            <option value="7">Agustus</option>
                            <option value="8">September</option>
                            <option value="9">Oktober</option>
                            <option value="10">November</option>
                            <option value="11">Desember</option>
                        </select>
                        <select id="year-selector" class="calendar-selector w-full md:w-28"></select>
                        <button id="next-month" class="calendar-nav-button w-full md:w-auto">Next →</button>
                    </div>
                </div>

                <div id="kaldik-calendar" class="calendar-container"></div>

                <div id="selected-date-details" class="hidden detail-section">
                    <x-filament::section>
                        <x-slot name="heading">
                            <span id="selected-date-title">Detail Kegiatan</span>
                        </x-slot>
                        <div id="selected-date-content" class="space-y-4"></div>
                    </x-filament::section>
                </div>
            </div>
        </x-filament::section>

        @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl       = document.getElementById('kaldik-calendar');
                const selectedDetails  = document.getElementById('selected-date-details');
                const selectedTitle    = document.getElementById('selected-date-title');
                const selectedContent  = document.getElementById('selected-date-content');
                const monthSelector    = document.getElementById('month-selector');
                const yearSelector     = document.getElementById('year-selector');

                const kaldiksData = @js($this->kaldiks);

                // Populate year selector
                const currentYear = new Date().getFullYear();
                for (let y = currentYear - 2; y <= currentYear + 3; y++) {
                    const opt = document.createElement('option');
                    opt.value = y;
                    opt.textContent = y;
                    if (y === currentYear) opt.selected = true;
                    yearSelector.appendChild(opt);
                }
                monthSelector.value = new Date().getMonth();

                const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: '2025-07-01', // mulai dari Juli tahun ajaran
                locale: 'id',
                headerToolbar: {
                    left: '',
                    center: 'title',
                    right: 'multiMonth,dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today:      'Hari Ini',
                    month:      'Bulan',
                    week:       'Minggu',
                    day:        'Hari',
                    multiMonth: 'Tahun', // label tombol baru
                },
                views: {
                    multiMonth: {
                        type:               'multiMonth',
                        duration:           { months: 12 },
                        multiMonthMaxColumns: 2,       // 2 kolom per baris
                        visibleRange: function(currentDate) {
                            // selalu tampilkan Juli - Juni
                            const start = new Date(currentDate.getFullYear(), 6, 1); // Juli
                            if (currentDate.getMonth() < 6) {
                                start.setFullYear(currentDate.getFullYear() - 1);
                            }
                            const end = new Date(start.getFullYear() + 1, 6, 1); // Juni tahun depan
                            return { start, end };
                        }
                    }
                },
                dayHeaderFormat: { weekday: 'long' },
                height:       'auto',
                selectable:   true,
                dayMaxEvents: true,
                events:       kaldiksData,
                dateClick:    info => showDateDetails(info.dateStr),
                eventClick:   info => showEventDetails(info.event),
            });

                calendar.render();

                monthSelector.addEventListener('change', () => {
                    calendar.gotoDate(new Date(+yearSelector.value, +monthSelector.value, 1));
                });
                yearSelector.addEventListener('change', () => {
                    calendar.gotoDate(new Date(+yearSelector.value, +monthSelector.value, 1));
                });
                document.getElementById('prev-month').addEventListener('click', () => { calendar.prev(); updateSelectors(); });
                document.getElementById('next-month').addEventListener('click', () => { calendar.next(); updateSelectors(); });

                function updateSelectors() {
                    const d = calendar.getDate();
                    monthSelector.value = d.getMonth();
                    yearSelector.value  = d.getFullYear();
                }
                calendar.on('datesSet', updateSelectors);

                // ── Tampilkan kegiatan pada tanggal yang diklik ──
                function showDateDetails(dateStr) {
                    const date = new Date(dateStr);
                    const label = date.toLocaleDateString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    });

                    selectedTitle.textContent = `Kegiatan — ${label}`;

                    // Event yang mulai ATAU sedang berlangsung di tanggal ini
                    const events = kaldiksData.filter(e => {
                    const start = e.start.substring(0, 10);
                    const end   = e.end.substring(0, 10);
                    return dateStr >= start && dateStr <= end;
                });

                    if (events.length > 0) {
                        selectedContent.innerHTML = events.map(e => {
                            const p = e.extendedProps;
                            return `
                            <div class="detail-card">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-white text-base">
                                            ${p.nama_acara}
                                        </h4>
                                        <span style="background:${e.backgroundColor}"
                                            class="inline-block text-white text-xs font-medium px-2 py-0.5 rounded-full mt-1">
                                            ${p.kegiatan}
                                        </span>
                                    </div>
                                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded-lg">
                                        ${p.tahun_ajaran}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm border-t border-gray-100 dark:border-gray-700 pt-3 mt-2">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Kategori</span>
                                        <p class="font-medium text-gray-900 dark:text-white">${p.kategori}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Ditujukan untuk</span>
                                        <p class="font-medium text-gray-900 dark:text-white">${p.subject}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Mulai</span>
                                        <p class="font-medium text-gray-900 dark:text-white">${p.tanggal_mulai}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Selesai</span>
                                        <p class="font-medium text-gray-900 dark:text-white">${p.tanggal_selesai}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Catatan</span>
                                        <p class="font-medium text-gray-900 dark:text-white">${p.notes}</p>
                                    </div>
                                </div>
                            </div>`;
                        }).join('');
                    } else {
                        selectedContent.innerHTML = `
                            <div class="text-center py-10">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada kegiatan pada tanggal ini</p>
                            </div>`;
                    }

                    selectedDetails.classList.remove('hidden');
                }

                // ── Tampilkan detail saat event di kalender diklik ──
                function showEventDetails(event) {
                    const p = event.extendedProps;
                    selectedTitle.textContent = `Detail — ${p.nama_acara}`;
                    selectedContent.innerHTML = `
                        <div class="detail-card-orange">
                            <h4 class="text-lg font-bold text-orange-900 dark:text-orange-100 mb-4">${p.nama_acara}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Jenis Kegiatan</span>
                                    <p class="font-semibold text-gray-900 dark:text-white">${p.kegiatan}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Kategori</span>
                                    <p class="font-medium text-gray-900 dark:text-white">${p.kategori}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Ditujukan Untuk</span>
                                    <p class="font-semibold text-gray-900 dark:text-white">${p.subject}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Tahun Ajaran</span>
                                    <p class="font-semibold text-gray-900 dark:text-white">${p.tahun_ajaran}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Mulai</span>
                                    <p class="font-semibold text-gray-900 dark:text-white">${p.tanggal_mulai}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Selesai</span>
                                    <p class="font-semibold text-gray-900 dark:text-white">${p.tanggal_selesai}</p>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Catatan</span>
                                    <p class="font-semibold text-gray-900 dark:text-white">${p.notes}</p>
                                </div>
                            </div>
                        </div>`;
                    selectedDetails.classList.remove('hidden');
                }
            });
        </script>
        @endpush
    </div>
</x-filament-panels::page>
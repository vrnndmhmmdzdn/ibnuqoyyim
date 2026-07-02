<x-filament-panels::page>
    <div class="space-y-4">

        {{-- Filter Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap items-end gap-4">

                @if (auth()->user()->role === 'admin')
                    <div class="flex-1 min-w-40" wire:key="filter-guru-wrapper">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Guru</label>
                        <select wire:model.live="guru_id"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Semua Guru</option>
                            @foreach ($this->guruList as $id => $nama)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Tambahkan wire:key di filter kelas --}}
                <div class="flex-1 min-w-40" wire:key="filter-kelas-wrapper">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas</label>
                    <select wire:model.live="kelas_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach ($this->kelasList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tambahkan wire:key di filter capaian --}}
                <div class="flex-1 min-w-40" wire:key="filter-capaian-wrapper">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capaian</label>
                    <select wire:model.live="capaian"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Semua Capaian</option>
                        @foreach ($this->capaianList as $value => $label)
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
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4" wire:ignore>
            <div id="jurnal-calendar"></div>
        </div>

        {{-- Detail Panel --}}
        <div id="jurnal-detail" class="hidden">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 id="detail-title" class="font-semibold text-gray-800 dark:text-white"></h3>
                    <button onclick="document.getElementById('jurnal-detail').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
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
            document.addEventListener('DOMContentLoaded', function() {

                const calendarEl = document.getElementById('jurnal-calendar');
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
                            week: 'Minggu',
                            day: 'Hari',
                        },
                        height: 'auto',
                        dayMaxEvents: 3,
                        events: data,
                        eventClick: function(info) {
                            info.jsEvent.preventDefault();
                            showDetail(info.event);

                            // Ambil data dari extendedProps FullCalendar
                            const props = info.event.extendedProps;

                            // 1. SINKRONISASI ID DENGAN ELEMEN HTML (Menggunakan awalan detail- bukan modal-)
                            if (document.getElementById('detail-materi')) document.getElementById(
                                'detail-materi').innerText = props.materi || '-';
                            if (document.getElementById('detail-guru')) document.getElementById(
                                'detail-guru').innerText = props.guru || '-';
                            if (document.getElementById('detail-kelas')) document.getElementById(
                                'detail-kelas').innerText = props.kelas || '-';
                            if (document.getElementById('detail-mapel')) document.getElementById(
                                'detail-mapel').innerText = props.mata_pelajaran || '-';
                            if (document.getElementById('detail-tanggal')) document.getElementById(
                                'detail-tanggal').innerText = props.tanggal || '-';
                            if (document.getElementById('detail-jam')) document.getElementById('detail-jam')
                                .innerText = (props.jam_mulai ? props.jam_mulai.substring(0, 5) : '-') +
                                ' - ' + (props.jam_selesai ? props.jam_selesai.substring(0, 5) : '-');
                            if (document.getElementById('detail-pertemuan')) document.getElementById(
                                'detail-pertemuan').innerText = props.pertemuan_ke || '-';
                            if (document.getElementById('detail-kd')) document.getElementById('detail-kd')
                                .innerText = props.kompetensi_dasar || '-';
                            if (document.getElementById('detail-kegiatan')) document.getElementById(
                                'detail-kegiatan').innerText = props.deskripsi_kegiatan || '-';
                            if (document.getElementById('detail-metode')) document.getElementById(
                                'detail-metode').innerText = props.metode || '-';
                            if (document.getElementById('detail-media')) document.getElementById(
                                'detail-media').innerText = props.media || '-';
                            if (document.getElementById('detail-kehadiran')) document.getElementById(
                                    'detail-kehadiran').innerText = (props.jumlah_hadir || 0) + ' Hadir, ' +
                                (props.jumlah_tidak_hadir || 0) + ' Absen (Total: ' + (props.total_siswa ||
                                    0) + ' - ' + (props.persentase_hadir || '0%') + ')';
                            if (document.getElementById('detail-tindak-lanjut')) document.getElementById(
                                'detail-tindak-lanjut').innerText = props.tindak_lanjut || '-';
                            if (document.getElementById('detail-catatan')) document.getElementById(
                                'detail-catatan').innerText = props.catatan || '-';

                            // 2. LOGIKA BADGE STATUS/CAPAIAN (ID disesuaikan ke detail-status)
                            const statusBadge = document.getElementById('detail-status');
                            if (statusBadge) {
                                statusBadge.innerText = props.status || '-';
                                statusBadge.className =
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
                                if (props.capaian_raw === 'tercapai') {
                                    statusBadge.classList.add('bg-green-100', 'text-green-800',
                                        'dark:bg-green-900', 'dark:text-green-200');
                                } else if (props.capaian_raw === 'sebagian') {
                                    statusBadge.classList.add('bg-amber-100', 'text-amber-800',
                                        'dark:bg-amber-900', 'dark:text-amber-200');
                                } else {
                                    statusBadge.classList.add('bg-red-100', 'text-red-800',
                                        'dark:bg-red-900', 'dark:text-red-200');
                                }
                            }

                            // 3. LOGIKA PREVIEW LAMPIRAN BERKAS JURNAL
                            const container = document.getElementById('modal-lampirans-container');
                            if (container) {
                                container.innerHTML = ''; // Bersihkan kontainer lama

                                if (props.lampirans && props.lampirans.length > 0) {
                                    props.lampirans.forEach(file => {
                                        let itemHtml = '';

                                        if (file.is_image) {
                                            itemHtml = `
                                    <div class="flex flex-col p-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900">
                                        <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase mb-1">${file.tipe}</span>
                                        <a href="${file.url}" target="_blank" class="group relative block overflow-hidden rounded border border-gray-300 dark:border-gray-600 bg-gray-200">
                                            <img src="${file.url}" class="w-full h-24 object-cover group-hover:scale-105 transition-transform duration-200" alt="${file.nama_file}">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-xs font-medium">Lihat Foto</div>
                                        </a>
                                        <span class="text-xs text-gray-500 mt-1 truncate" title="${file.nama_file}">${file.nama_file}</span>
                                    </div>`;
                                        } else if (file.is_pdf) {
                                            itemHtml = `
                                    <div class="flex items-center gap-3 p-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900">
                                        <div class="p-2 bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400 rounded-lg shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">${file.tipe}</p>
                                            <p class="text-xs text-gray-700 dark:text-gray-300 font-medium truncate" title="${file.nama_file}">${file.nama_file}</p>
                                        </div>
                                        <a href="${file.url}" target="_blank" class="px-2 py-1 bg-white dark:bg-gray-800 text-xs border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700 shrink-0 shadow-sm">Buka</a>
                                    </div>`;
                                        } else {
                                            itemHtml = `
                                    <div class="flex items-center gap-3 p-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 rounded-lg shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase">${file.tipe}</p>
                                            <p class="text-xs text-gray-700 dark:text-gray-300 font-medium truncate" title="${file.nama_file}">${file.nama_file}</p>
                                        </div>
                                        <a href="${file.url}" download class="px-2 py-1 bg-white dark:bg-gray-800 text-xs border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700 shrink-0 shadow-sm">Unduh</a>
                                    </div>`;
                                        }

                                        container.insertAdjacentHTML('beforeend', itemHtml);
                                    });
                                } else {
                                    container.innerHTML =
                                        `<span class="text-xs text-gray-400 italic">Tidak ada lampiran berkas.</span>`;
                                }
                            }

                            // Pastikan panel bawah Anda terekspos / isModalOpen aktif
                            this.isModalOpen = true;
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
                        'belum': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                    } [p.capaian_raw] ?? 'bg-gray-100 text-gray-700';

                    const statusColor = p.status === 'Submitted' ?
                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                        'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300';

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

                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Lampiran / Bukti Jurnal</h4>
                            
                            {{-- Container dinamis untuk list file lampiran --}}
                            <div id="modal-lampirans-container" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                </div>
                        </div>

                    </div>
                `;

                    detailPanel.classList.remove('hidden');
                    detailPanel.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }

                // Init pertama
                initCalendar(jurnalsData);

                // Re-init kalau Livewire update filter
                // Ganti blok Livewire.on('jurnals-updated') lama Anda dengan ini:
                Livewire.on('jurnals-updated', (event) => {
                    // Memastikan payload data terekstrak dengan benar baik pada Livewire v2 maupun v3
                    let data = Array.isArray(event) ? event : (event.detail ? event.detail : event);

                    // Unboxing jika data dikirim di dalam array berlapis oleh Livewire
                    if (data && data[0] && Array.isArray(data[0])) {
                        data = data[0];
                    }

                    // Eksekusi update data pada FullCalendar tanpa reload/destroy element kalender
                    if (calendar && data) {
                        calendar.removeAllEvents();
                        calendar.addEventSource(data);
                    }
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

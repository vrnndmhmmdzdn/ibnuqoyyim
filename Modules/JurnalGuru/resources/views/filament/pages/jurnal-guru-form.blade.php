<x-filament-panels::page>
<div class="space-y-5">

    {{-- Step 1: Pilih Tanggal --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Tanggal Mengajar</h3>
        <input type="date" wire:model.live="tanggal"
            max="{{ today()->format('Y-m-d') }}"
            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
    </div>

    {{-- Step 2: Pilih Jadwal --}}
    @if($tanggal)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                Jadwal Hari Ini
                <span class="font-normal text-gray-400 ml-1">
                    ({{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }})
                </span>
            </h3>
            <button wire:click="inputManual"
                class="text-xs text-primary-600 dark:text-primary-400 hover:underline font-medium">
                + Input Manual
            </button>
        </div>

        @if($this->jadwalHariIni->isEmpty())
            <div class="text-center py-6 text-gray-400 dark:text-gray-500 text-sm">
                Tidak ada jadwal untuk hari ini.
                <button wire:click="inputManual" class="text-primary-600 dark:text-primary-400 hover:underline ml-1">
                    Input manual
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($this->jadwalHariIni as $jadwal)
                @php
                    $isSelected = $selected_jadwal_id === $jadwal->id;
                @endphp
                <button wire:click="pilihJadwal({{ $jadwal->id }})"
                    class="text-left p-4 rounded-xl border-2 transition-all
                        {{ $isSelected
                            ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                            : 'border-gray-200 dark:border-gray-600 hover:border-primary-300 dark:hover:border-primary-600 bg-gray-50 dark:bg-gray-700/50' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white text-sm">
                                {{ $jadwal->mataPelajaran?->pelajaran ?? '-' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $jadwal->kelas?->nama_kelas ?? '-' }}
                            </p>
                        </div>
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ substr($jadwal->jam_mulai, 0, 5) }}–{{ substr($jadwal->jam_selesai, 0, 5) }}
                        </span>
                    </div>
                    @if($isSelected)
                    <div class="mt-2 flex items-center gap-1 text-primary-600 dark:text-primary-400">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-medium">Dipilih</span>
                    </div>
                    @endif
                </button>
                @endforeach
            </div>
        @endif
    </div>
    @endif

    {{-- Step 3: Form Detail --}}
    @if($selected_jadwal_id || $mode_manual)
    <div class="space-y-4">

        {{-- Informasi Mengajar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Informasi Mengajar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Guru --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Guru</label>
                    <select wire:model.live="guru_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Pilih Guru</option>
                        @foreach($this->guruList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('guru_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Kelas</label>
                    <select wire:model.live="kelas_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Pilih Kelas</option>
                        @foreach($this->kelasList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Mata Pelajaran --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Mata Pelajaran</label>
                    <select wire:model.live="mata_pelajaran_id"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($this->mapelList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('mata_pelajaran_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pertemuan ke --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Pertemuan Ke
                        <span class="text-gray-400 font-normal">(auto, bisa diubah)</span>
                    </label>
                    <input type="number" wire:model="pertemuan_ke" min="1"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                        placeholder="Auto">
                </div>

                {{-- Jam Mulai --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jam Mulai</label>
                    <input type="time" wire:model="jam_mulai"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @error('jam_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jam Selesai --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jam Selesai</label>
                    <input type="time" wire:model="jam_selesai"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @error('jam_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- Detail Pembelajaran --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Detail Pembelajaran</h3>
            <div class="space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Materi</label>
                    <input type="text" wire:model="materi"
                        placeholder="Contoh: Pengenalan Bilangan Cacah"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @error('materi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Kompetensi Dasar</label>
                    <textarea wire:model="kompetensi_dasar" rows="2"
                        placeholder="Contoh: 3.1 Memahami bilangan cacah..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                    @error('kompetensi_dasar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Deskripsi Kegiatan</label>
                    <textarea wire:model="deskripsi_kegiatan" rows="3"
                        placeholder="Jelaskan kegiatan pembelajaran dari awal hingga akhir..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                    @error('deskripsi_kegiatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Metode Pembelajaran</label>
                        <select wire:model="metode_pembelajaran"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Pilih Metode</option>
                            @foreach(\Modules\JurnalGuru\Models\JurnalGuru::METODE as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('metode_pembelajaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                            Media Pembelajaran
                            <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <input type="text" wire:model="media_pembelajaran"
                            placeholder="Contoh: Proyektor, Papan Tulis"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    </div>
                </div>

            </div>
        </div>

        {{-- Kehadiran & Evaluasi --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Kehadiran & Evaluasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jumlah Hadir</label>
                    <input type="number" wire:model="jumlah_hadir" min="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @error('jumlah_hadir') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jumlah Tidak Hadir</label>
                    <input type="number" wire:model="jumlah_tidak_hadir" min="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @error('jumlah_tidak_hadir') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Capaian Pembelajaran</label>
                    <div class="flex gap-2">
                        @foreach(\Modules\JurnalGuru\Models\JurnalGuru::CAPAIAN as $value => $label)
                        @php
                            $color = match($value) {
                                'tercapai' => 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300',
                                'sebagian' => 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300',
                                'belum'    => 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300',
                            };
                            $inactive = 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-gray-300';
                        @endphp
                        <button type="button" wire:click="$set('capaian', '{{ $value }}')"
                            class="flex-1 py-2 px-3 rounded-lg border-2 text-xs font-medium transition-all
                                {{ $capaian === $value ? $color : $inactive }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                    @error('capaian') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Tindak Lanjut <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea wire:model="tindak_lanjut" rows="2"
                        placeholder="Rencana tindak lanjut untuk pertemuan berikutnya..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea wire:model="catatan" rows="2"
                        placeholder="Catatan tambahan..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                </div>

            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex items-center justify-end gap-3 pb-4">
            <button type="button" wire:click="simpan('draft')"
                class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Simpan Draft
            </button>
            <button type="button" wire:click="simpan('submitted')"
                class="px-5 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium transition-colors">
                Submit Jurnal
            </button>
        </div>

        {{-- Alert saran lampiran — muncul setelah form --}}
        @if(!$selected_jadwal_id && !$mode_manual && !$jurnal_id_tersimpan)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Saran: Lampirkan file pendukung</p>
                <p class="text-xs text-blue-600 dark:text-blue-300 mt-0.5">
                    Setelah menyimpan jurnal, kamu bisa melampirkan RPP, foto kegiatan, modul, atau file lainnya sebagai bukti mengajar untuk kepala sekolah.
                </p>
            </div>
        </div>
        @endif

        {{-- Section Lampiran — muncul setelah jurnal tersimpan --}}
        @if($jurnal_id_tersimpan)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Jurnal</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        Tambahkan file pendukung sebagai bukti mengajar
                    </p>
                </div>
                <span class="text-xs text-gray-400">
                    Format: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX
                </span>
            </div>

            {{-- Alert saran --}}
            @if($this->lampiranJurnal->isEmpty())
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-4 flex gap-2">
                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xs text-amber-700 dark:text-amber-300">
                    Jurnal tersimpan tanpa lampiran. Disarankan untuk melampirkan setidaknya satu file sebagai bukti mengajar (foto kegiatan, RPP, atau modul).
                </p>
            </div>
            @endif

            {{-- Upload area --}}
            <div class="mb-4">
                <label class="block w-full">
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-primary-400 dark:hover:border-primary-600 transition-colors cursor-pointer">
                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                            Klik untuk pilih file
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            atau drag & drop di sini
                        </p>
                        <input type="file" wire:model="lampiran_file" class="hidden"
                            accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.xlsm,.ppt,.pptx">
                    </div>
                </label>

                {{-- Preview nama file yang dipilih --}}
                @if($lampiran_file)
                <div class="mt-2 flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2">
                    <span class="text-xs text-gray-600 dark:text-gray-300 truncate">
                        {{ is_object($lampiran_file) ? $lampiran_file->getClientOriginalName() : $lampiran_file }}
                    </span>
                    <button type="button" wire:click="uploadLampiran"
                        class="ml-3 flex-shrink-0 px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                        Upload
                    </button>
                </div>
                @endif

                @error('lampiran_file')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Daftar lampiran yang sudah diupload --}}
            @if($this->lampiranJurnal->isNotEmpty())
            <div class="space-y-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
                    File Terlampir ({{ $this->lampiranJurnal->count() }})
                </p>
                @foreach($this->lampiranJurnal as $lampiran)
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">

                    {{-- Icon / Preview --}}
                    @if($lampiran->is_image)
                    <img src="{{ Storage::url($lampiran->path) }}"
                        alt="{{ $lampiran->nama_file }}"
                        class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                    @else
                    <div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    @endif

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">
                            {{ $lampiran->nama_file }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ \Modules\JurnalGuru\Models\JurnalLampiran::TIPE[$lampiran->tipe] ?? $lampiran->tipe }}
                            · {{ $lampiran->ukuran_readable }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ Storage::url($lampiran->path) }}" target="_blank"
                            class="p-1.5 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                            title="Download">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                        <button wire:click="hapusLampiran({{ $lampiran->id }})"
                            wire:confirm="Hapus lampiran ini?"
                            class="p-1.5 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                            title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>

                </div>
                @endforeach
            </div>
            @endif

            {{-- Tombol selesai --}}
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button type="button" wire:click="$set('jurnal_id_tersimpan', null)"
                    class="px-5 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium transition-colors">
                    Selesai, Input Jurnal Baru
                </button>
            </div>

        </div>
        @endif

    </div>
    @endif

</div>
</x-filament-panels::page>
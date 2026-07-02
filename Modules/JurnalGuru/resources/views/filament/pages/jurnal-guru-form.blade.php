<x-filament-panels::page>
    <div class="space-y-5">

        {{-- Step 1: Pilih Tanggal --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Tanggal Mengajar</h3>
            <input type="date" wire:model.live="tanggal" max="{{ today()->format('Y-m-d') }}"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
        </div>

        {{-- Step 2: Pilih Jadwal --}}
        @if ($tanggal)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Jadwal Hari Ini
                        <span class="font-normal text-gray-400 ml-1">
                            ({{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }})
                        </span>
                    </h3>
                    <button wire:click="inputManual"
                        class="text-xs text-primary-600 dark:text-primary-400 font-medium hover:underline">
                        {{ $mode_manual ? 'Lihat Jadwal Sistem' : 'Input Jurnal Manual (Di luar jadwal)' }}
                    </button>
                </div>

                @if (!$mode_manual)
                    @php
                        $jadwals = $this->jadwals;
                    @endphp

                    @if (count($jadwals) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($jadwals as $jadwal)
                                <div wire:click="pilihJadwal({{ $jadwal->id }})"
                                    class="cursor-pointer p-4 rounded-xl border transition-all duration-200 text-left relative overflow-hidden
                                    {{ $selected_jadwal_id === $jadwal->id
                                        ? 'border-primary-500 bg-primary-50/50 dark:bg-primary-950/20 ring-2 ring-primary-500'
                                        : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 bg-gray-50/50 dark:bg-gray-900/20' }}">

                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 mb-2">
                                                Jam Ke-{{ $jadwal->jam_ke }}
                                            </span>
                                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">
                                                {{ $jadwal->mataPelajaran?->pelajaran ?? '-' }}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                                <span>Kelas {{ $jadwal->kelas?->nama_kelas ?? '-' }}</span>
                                                <span>•</span>
                                                <span>{{ substr($jadwal->jam_mulai, 0, 5) }} -
                                                    {{ substr($jadwal->jam_selesai, 0, 5) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="text-center py-6 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada jadwal pelajaran formal untuk
                                Anda pada hari ini.</p>
                            <button wire:click="inputManual"
                                class="mt-2 text-xs text-primary-600 dark:text-primary-400 font-semibold hover:underline">
                                Klik di sini untuk input manual
                            </button>
                        </div>
                    @endif
                @else
                    <div
                        class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-xl text-xs text-amber-800 dark:text-amber-300 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Anda masuk dalam <strong>Mode Manual</strong>. Silakan isi semua pilihan informasi mengajar
                            secara mandiri pada form di bawah.</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Step 3: Form Jurnal Utama atau Tampilan Sukses --}}
        @if ($tanggal && ($selected_jadwal_id || $mode_manual))
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">

                @if (!$jurnal_id_tersimpan)
                    {{-- JIKA BELUM SUBMIT: TAMPILKAN FORM UTAMA --}}
                    <h3
                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                        Isi Laporan Jurnal Pembelajaran
                    </h3>

                    <form wire:submit.prevent="simpanJurnal" class="space-y-5">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 bg-gray-50 dark:bg-gray-900/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Guru
                                    Pengajar</label>
                                <select wire:model="guru_id" @disabled(!$mode_manual)
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white disabled:opacity-75">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach ($this->guruList as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('guru_id')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kelas</label>
                                <select wire:model="kelas_id" @disabled(!$mode_manual)
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white disabled:opacity-75">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($this->kelasList as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mata
                                    Pelajaran</label>
                                <select wire:model="mata_pelajaran_id" @disabled(!$mode_manual)
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white disabled:opacity-75">
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach ($this->mapelList as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('mata_pelajaran_id')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pertemuan
                                    Ke-</label>
                                <input type="number" wire:model="pertemuan_ke" min="1"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @error('pertemuan_ke')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jam
                                    Mulai</label>
                                <input type="time" wire:model="jam_mulai" @disabled(!$mode_manual)
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white disabled:opacity-75">
                                @error('jam_mulai')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jam
                                    Selesai</label>
                                <input type="time" wire:model="jam_selesai" @disabled(!$mode_manual)
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white disabled:opacity-75">
                                @error('jam_selesai')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Materi
                                    Pokok / Pembahasan</label>
                                <input type="text" wire:model="materi"
                                    placeholder="Contoh: Perkalian Bilangan Bulat"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @error('materi')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kompetensi
                                    Dasar / Capaian</label>
                                <input type="text" wire:model="kompetensi_dasar"
                                    placeholder="Contoh: KD 3.2 Menjelaskan pecahan"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @error('kompetensi_dasar')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi
                                Kegiatan Pembelajaran</label>
                            <textarea wire:model="deskripsi_kegiatan" rows="3"
                                placeholder="Gambahkan singkat alur pembelajaran hari ini..."
                                class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                            @error('deskripsi_kegiatan')
                                <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode
                                    Pembelajaran</label>
                                <select wire:model="metode_pembelajaran"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Pilih Metode --</option>
                                    @foreach (\Modules\JurnalGuru\Models\JurnalGuru::METODE as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                                @error('metode_pembelajaran')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Media
                                    Pembelajaran</label>
                                <input type="text" wire:model="media_pembelajaran"
                                    placeholder="Contoh: Proyektor, Papan Tulis, LKPD"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @error('media_pembelajaran')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 dark:bg-gray-900/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Siswa
                                    Hadir</label>
                                <input type="number" wire:model="jumlah_hadir" min="0"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @error('jumlah_hadir')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Siswa
                                    Absen</label>
                                <input type="number" wire:model="jumlah_tidak_hadir" min="0"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @error('jumlah_tidak_hadir')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ketercapaian
                                    Target Pokok</label>
                                <select wire:model="capaian"
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @foreach (\Modules\JurnalGuru\Models\JurnalGuru::CAPAIAN as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                                @error('capaian')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tindak
                                    Lanjut Pembelajaran</label>
                                <textarea wire:model="tindak_lanjut" rows="2"
                                    placeholder="Contoh: Memberikan pengayaan materi di pertemuan esok..."
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                                @error('tindak_lanjut')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan
                                    Hambatan / Evaluasi</label>
                                <textarea wire:model="catatan" rows="2" placeholder="Contoh: Kondisi kelas agak bising..."
                                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                                @error('catatan')
                                    <span class="text-xs text-red-500 mt-0.5 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium text-sm rounded-lg transition shadow-sm inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Laporan Jurnal</span>
                            </button>
                        </div>
                    </form>
                @else
                    {{-- JIKA SUDAH BERHASIL SUBMIT: TAMPILKAN STATUS LAYOUT UNTUK UPLOAD BERKAS --}}
                    <div class="p-6 text-center bg-emerald-50 dark:bg-emerald-950/20 rounded-xl">
                        <div
                            class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 dark:text-white">Laporan Jurnal Berhasil Disimpan!
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mt-1">
                            Data mengajar utama Anda sudah aman di sistem. Sekarang Anda dapat menambahkan file lampiran
                            pendukung di bawah ini.
                        </p>

                        {{-- ================= FORM DOKUMEN INPUT (DIBAIKI) ================= --}}
                        <div class="mt-6 max-w-2xl mx-auto text-left bg-white dark:bg-gray-900 p-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 shadow-sm"
                            wire:key="upload-section-form">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-2">Pilih
                                Dokumen / Foto Kegiatan</label>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <div class="flex-1">
                                    <input type="file" wire:model="lampiran_file" id="lampiran_file"
                                        class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 dark:file:bg-gray-800 dark:file:text-gray-300 cursor-pointer">
                                    @error('lampiran_file')
                                        <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="button" wire:click="simpanLampiran"
                                    wire:loading.attr="disabled" wire:target="lampiran_file, simpanLampiran"
                                    class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition disabled:opacity-50 flex items-center justify-center gap-1.5 shrink-0">
                                    <svg wire:loading.remove wire:target="simpanLampiran" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span wire:loading.remove wire:target="simpanLampiran">Upload Berkas</span>
                                    <span wire:loading wire:target="simpanLampiran">Menyimpan...</span>
                                </button>
                            </div>

                            <div wire:loading wire:target="lampiran_file"
                                class="text-xs text-amber-500 font-medium mt-2 flex items-center gap-1.5 animate-pulse">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.253 8H18" />
                                </svg>
                                Membaca file... mohon tunggu sebentar.
                            </div>
                        </div>

                        {{-- ================= DAFTAR LAMPIRAN TERUPLOAD (MENGGUNAKAN $this->lampirans) ================= --}}
                        @if ($this->lampirans && count($this->lampirans) > 0)
                            <div class="mt-8 text-left max-w-2xl mx-auto" wire:key="uploaded-list-wrapper">
                                <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">
                                    Berkas Terlampir ({{ count($this->lampirans) }})
                                </h4>

                                <div class="space-y-2">
                                    @foreach ($this->lampirans as $lampiran)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-800 rounded-xl bg-white dark:bg-gray-900 hover:border-gray-300 dark:hover:border-gray-700 transition shadow-sm"
                                            wire:key="lampiran-item-{{ $lampiran->id }}">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="p-2 bg-gray-50 dark:bg-gray-800 text-gray-500 rounded-lg shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs text-gray-700 dark:text-gray-300 font-semibold truncate">{{ $lampiran->nama_file }}</p>
                                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide mt-0.5">{{ $lampiran->tipe }} • {{ $lampiran->ukuran_readable }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-1.5 shrink-0 ml-4">
                                                <a href="{{ $lampiran->url }}" target="_blank"
                                                    class="p-1.5 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                                    title="Buka File">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                                <button type="button" wire:click="hapusLampiran({{ $lampiran->id }})"
                                                    wire:confirm="Hapus lampiran ini?"
                                                    class="p-1.5 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                                    title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Tombol selesai --}}
                        <button type="button" wire:click="selesaiInputJurnal"
                            wire:loading.attr="disabled" wire:target="selesaiInputJurnal"
                            class="px-5 py-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium transition-colors disabled:opacity-60">
                            <span wire:loading.remove wire:target="selesaiInputJurnal">Selesai, Input Jurnal Baru</span>
                            <span wire:loading wire:target="selesaiInputJurnal">Menyimpan lampiran...</span>
                        </button>


                    </div>
                @endif

            </div>
        @endif

    </div>
</x-filament-panels::page>
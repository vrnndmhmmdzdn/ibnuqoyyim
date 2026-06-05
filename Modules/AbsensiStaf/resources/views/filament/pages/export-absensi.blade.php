<x-filament-panels::page>
<div class="max-w-2xl space-y-5">

    {{-- Form Export --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-5">Pengaturan Export</h3>

        <div class="space-y-5">

            {{-- Tipe Export --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Tipe Export</label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" wire:click="$set('tipe', 'semua')"
                        class="p-4 rounded-xl border-2 text-left transition-all
                            {{ $tipe === 'semua'
                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="font-semibold text-sm text-gray-800 dark:text-white">Semua Staf</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Export rekap semua staf + sheet per orang dalam satu file
                        </p>
                    </button>

                    <button type="button" wire:click="$set('tipe', 'personal')"
                        class="p-4 rounded-xl border-2 text-left transition-all
                            {{ $tipe === 'personal'
                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <p class="font-semibold text-sm text-gray-800 dark:text-white">Per Staf</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Export absensi detail satu orang saja
                        </p>
                    </button>
                </div>
            </div>

            {{-- Pilih Staf (hanya kalau tipe personal) --}}
            @if($tipe === 'personal')
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Pilih Staf</label>
                <select wire:model.live="guru_id"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">-- Pilih Staf --</option>
                    @foreach($this->guruList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Periode --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bulan</label>
                    <select wire:model.live="bulan"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        @foreach($this->bulanList as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tahun</label>
                    <select wire:model.live="tahun"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        @foreach($this->tahunList as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Preview info --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Info Export</p>
                <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                    <div class="flex justify-between">
                        <span>Tipe</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ $tipe === 'semua' ? 'Semua Staf' : 'Per Staf' }}
                        </span>
                    </div>
                    @if($tipe === 'personal' && $guru_id)
                    <div class="flex justify-between">
                        <span>Staf</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ $this->guruList[$guru_id] ?? '-' }}
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span>Periode</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $this->namaBulan }}</span>
                    </div>
                    @if($tipe === 'semua')
                    <div class="flex justify-between">
                        <span>Sheet</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ count($this->guruList) + 1 }} sheet (1 rekap + {{ count($this->guruList) }} per orang)
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span>Format</span>
                        <span class="font-medium text-gray-800 dark:text-white">.xlsx (Excel)</span>
                    </div>
                </div>
            </div>

            {{-- Tombol Export --}}
            <div class="flex justify-end">
                <a wire:click="export"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Excel
                </a>
            </div>

        </div>
    </div>

    {{-- Panduan --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
        <p class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">Isi file Excel</p>
        <div class="space-y-1 text-xs text-blue-700 dark:text-blue-300">
            <p><span class="font-medium">Export Semua Staf:</span> Sheet pertama berisi rekap kehadiran semua staf. Sheet berikutnya berisi detail absensi per orang.</p>
            <p><span class="font-medium">Export Per Staf:</span> Satu file berisi detail absensi harian staf yang dipilih.</p>
            <p><span class="font-medium">Kolom yang tersedia:</span> Tanggal, Jam Masuk, Jam Pulang, Durasi, Keterlambatan, Status, Keterangan.</p>
        </div>
    </div>

</div>
</x-filament-panels::page>
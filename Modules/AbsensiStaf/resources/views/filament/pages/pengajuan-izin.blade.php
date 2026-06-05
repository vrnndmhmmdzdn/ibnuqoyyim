<x-filament-panels::page>
<div class="space-y-5">

    {{-- Tidak ada data guru --}}
    @if(!$this->guru)
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
        <p class="text-sm text-red-600 dark:text-red-400 font-medium">
            Akun kamu belum terhubung ke data guru. Hubungi admin.
        </p>
    </div>
    @else

    {{-- Alert izin masih menunggu --}}
    @if($this->adaIzinAktif)
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Ada pengajuan yang belum diproses</p>
            <p class="text-xs text-amber-600 dark:text-amber-300 mt-0.5">
                Kamu masih memiliki pengajuan izin yang menunggu persetujuan admin.
            </p>
        </div>
    </div>
    @endif

    {{-- Form Pengajuan --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Form Pengajuan Izin</h3>

        <div class="space-y-4">

            {{-- Jenis --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Jenis</label>
                <div class="flex gap-3">
                    <button type="button" wire:click="$set('jenis', 'izin')"
                        class="flex-1 py-3 px-4 rounded-xl border-2 text-sm font-medium transition-all
                            {{ $jenis === 'izin'
                                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-gray-300' }}">
                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Izin
                    </button>
                    <button type="button" wire:click="$set('jenis', 'sakit')"
                        class="flex-1 py-3 px-4 rounded-xl border-2 text-sm font-medium transition-all
                            {{ $jenis === 'sakit'
                                ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300'
                                : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-gray-300' }}">
                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Sakit
                    </button>
                </div>
                @error('jenis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Mulai</label>
                    <input type="date" wire:model.live="tanggal_mulai"
                        min="{{ today()->format('Y-m-d') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @error('tanggal_mulai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Selesai</label>
                    <input type="date" wire:model.live="tanggal_selesai"
                        min="{{ $tanggal_mulai ?: today()->format('Y-m-d') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @error('tanggal_selesai') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Info jumlah hari --}}
            @if($this->jumlahHari > 0)
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-4 py-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    Durasi: <span class="font-semibold text-gray-800 dark:text-white">{{ $this->jumlahHari }} hari</span>
                </span>
            </div>
            @endif

            {{-- Keterangan --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                    Keterangan
                    <span class="text-gray-400 font-normal">(min. 10 karakter)</span>
                </label>
                <textarea wire:model="keterangan" rows="3"
                    placeholder="{{ $jenis === 'izin' ? 'Contoh: Menghadiri acara pernikahan keluarga...' : 'Contoh: Demam dan sakit kepala sejak kemarin...' }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                <div class="flex justify-between mt-1">
                    @error('keterangan')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                    @else
                    <span></span>
                    @enderror
                    <span class="text-xs text-gray-400">{{ strlen($keterangan) }}/500</span>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex justify-end">
                <button type="button" wire:click="ajukan"
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="ajukan">Ajukan Izin</span>
                    <span wire:loading wire:target="ajukan">Mengajukan...</span>
                </button>
            </div>

        </div>
    </div>

    {{-- Riwayat Pengajuan --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 dark:text-white">Riwayat Pengajuan</h3>

            {{-- Filter status --}}
            <select wire:model.live="filter_status"
                class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs">
                <option value="">Semua</option>
                @foreach(\Modules\AbsensiStaf\Models\IzinStaf::STATUS as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @if($this->riwayatIzin->isEmpty())
            <div class="text-center py-10">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada pengajuan izin</p>
            </div>
        @else
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach($this->riwayatIzin as $izin)
                @php
                    $statusColor = match($izin->status) {
                        'menunggu'  => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                        'disetujui' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'ditolak'   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                        default     => 'bg-gray-100 text-gray-700',
                    };
                    $jenisColor = $izin->jenis === 'izin'
                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
                        : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300';
                @endphp
                <div class="px-5 py-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $jenisColor }}">
                                    {{ \Modules\AbsensiStaf\Models\IzinStaf::JENIS[$izin->jenis] }}
                                </span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $izin->tanggal_mulai->format('d M Y') }}
                                    @if(!$izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                                        — {{ $izin->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $izin->jumlah_hari }} hari
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                {{ $izin->keterangan }}
                            </p>
                            @if($izin->catatan_admin)
                            <div class="mt-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Catatan admin: <span class="text-gray-700 dark:text-gray-300">{{ $izin->catatan_admin }}</span>
                                </p>
                            </div>
                            @endif
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Diajukan {{ $izin->created_at->locale('id')->diffForHumans() }}
                            </p>
                        </div>

                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ \Modules\AbsensiStaf\Models\IzinStaf::STATUS[$izin->status] }}
                            </span>

                            {{-- Tombol batalkan hanya kalau masih menunggu --}}
                            @if($izin->status === 'menunggu')
                            <button wire:click="batalkan({{ $izin->id }})"
                                wire:confirm="Batalkan pengajuan izin ini?"
                                class="text-xs text-red-500 dark:text-red-400 hover:underline">
                                Batalkan
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    @endif

</div>
</x-filament-panels::page>
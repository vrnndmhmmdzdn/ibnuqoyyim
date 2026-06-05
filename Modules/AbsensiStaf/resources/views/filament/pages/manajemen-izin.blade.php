<x-filament-panels::page>
<div class="space-y-5">

    {{-- Stats menunggu --}}
    @if($this->totalMenunggu > 0)
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
            <span class="text-amber-700 dark:text-amber-300 font-bold text-sm">{{ $this->totalMenunggu }}</span>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                {{ $this->totalMenunggu }} pengajuan menunggu persetujuan
            </p>
            <p class="text-xs text-amber-600 dark:text-amber-300 mt-0.5">
                Segera proses agar staf bisa mendapatkan kepastian.
            </p>
        </div>
    </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-wrap items-end gap-4">

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                <select wire:model.live="filter_status"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Semua Status</option>
                    @foreach(\Modules\AbsensiStaf\Models\IzinStaf::STATUS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jenis</label>
                <select wire:model.live="filter_jenis"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Semua Jenis</option>
                    @foreach(\Modules\AbsensiStaf\Models\IzinStaf::JENIS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bulan</label>
                <select wire:model.live="filter_bulan"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">Semua Bulan</option>
                    @foreach($this->bulanList as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tahun</label>
                <select wire:model.live="filter_tahun"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @foreach($this->tahunList as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    {{-- Daftar Izin --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">
                Daftar Pengajuan
                <span class="text-gray-400 font-normal text-sm ml-1">({{ $this->daftarIzin->count() }} data)</span>
            </h3>
        </div>

        @if($this->daftarIzin->isEmpty())
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada pengajuan</p>
            </div>
        @else
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach($this->daftarIzin as $izin)
                @php
                    $statusColor = match($izin->status) {
                        'menunggu'  => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                        'disetujui' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                        'ditolak'   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                    };
                    $jenisColor = $izin->jenis === 'izin'
                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
                        : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300';
                @endphp
                <div class="px-5 py-4">
                    <div class="flex items-start gap-4">

                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-primary-600 dark:text-primary-400 font-bold">
                                {{ strtoupper(substr($izin->guru?->name ?? '?', 0, 1)) }}
                            </span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="font-semibold text-gray-800 dark:text-white text-sm">
                                    {{ $izin->guru?->name ?? '-' }}
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $jenisColor }}">
                                    {{ \Modules\AbsensiStaf\Models\IzinStaf::JENIS[$izin->jenis] }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $izin->tanggal_mulai->format('d M Y') }}
                                @if(!$izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                                    — {{ $izin->tanggal_selesai->format('d M Y') }}
                                @endif
                                <span class="text-gray-400 ml-1">({{ $izin->jumlah_hari }} hari)</span>
                            </p>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $izin->keterangan }}
                            </p>

                            {{-- Catatan admin --}}
                            @if($izin->catatan_admin)
                            <div class="mt-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Catatan admin: <span class="text-gray-700 dark:text-gray-300">{{ $izin->catatan_admin }}</span>
                                </p>
                            </div>
                            @endif

                            {{-- Info diproses --}}
                            @if($izin->diproses_at)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Diproses oleh {{ $izin->diprosesoleh?->name ?? '-' }}
                                · {{ $izin->diproses_at->locale('id')->diffForHumans() }}
                            </p>
                            @else
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Diajukan {{ $izin->created_at->locale('id')->diffForHumans() }}
                            </p>
                            @endif
                        </div>

                        {{-- Status + Aksi --}}
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ \Modules\AbsensiStaf\Models\IzinStaf::STATUS[$izin->status] }}
                            </span>

                            @if($izin->status === 'menunggu')
                            <div class="flex gap-2">
                                <button wire:click="bukaModal({{ $izin->id }}, 'disetujui')"
                                    class="px-3 py-1.5 rounded-lg bg-green-500 hover:bg-green-600 text-white text-xs font-medium transition-colors">
                                    Setujui
                                </button>
                                <button wire:click="bukaModal({{ $izin->id }}, 'ditolak')"
                                    class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white text-xs font-medium transition-colors">
                                    Tolak
                                </button>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Modal Proses Izin --}}
    @if($izin_id_proses)
    @php
        $izinProses = $this->daftarIzin->firstWhere('id', $izin_id_proses);
    @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background: rgba(0,0,0,0.5)">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6">

            <h3 class="font-semibold text-gray-800 dark:text-white text-lg mb-1">
                {{ $aksi_proses === 'disetujui' ? 'Setujui Izin' : 'Tolak Izin' }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ $izinProses?->guru?->name }} —
                {{ $izinProses?->tanggal_mulai?->format('d M Y') }}
                @if($izinProses && !$izinProses->tanggal_mulai->isSameDay($izinProses->tanggal_selesai))
                    — {{ $izinProses?->tanggal_selesai?->format('d M Y') }}
                @endif
            </p>

            {{-- Alert khusus approve --}}
            @if($aksi_proses === 'disetujui')
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 mb-4">
                <p class="text-xs text-green-700 dark:text-green-300">
                    Menyetujui izin ini akan otomatis mengisi data absensi staf sebagai
                    <strong>{{ \Modules\AbsensiStaf\Models\IzinStaf::JENIS[$izinProses?->jenis ?? 'izin'] }}</strong>
                    untuk setiap hari kerja dalam rentang tanggal tersebut.
                </p>
            </div>
            @endif

            {{-- Catatan admin --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                    Catatan
                    @if($aksi_proses === 'ditolak')
                        <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal">(wajib diisi)</span>
                    @else
                        <span class="text-gray-400 font-normal">(opsional)</span>
                    @endif
                </label>
                <textarea wire:model="catatan_admin" rows="3"
                    placeholder="{{ $aksi_proses === 'ditolak' ? 'Jelaskan alasan penolakan...' : 'Catatan tambahan (opsional)...' }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"></textarea>
                @error('catatan_admin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 justify-end">
                <button wire:click="tutupModal"
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Batal
                </button>
                <button wire:click="prosesIzin"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors disabled:opacity-60
                        {{ $aksi_proses === 'disetujui'
                            ? 'bg-green-500 hover:bg-green-600'
                            : 'bg-red-500 hover:bg-red-600' }}">
                    <span wire:loading.remove wire:target="prosesIzin">
                        {{ $aksi_proses === 'disetujui' ? 'Ya, Setujui' : 'Ya, Tolak' }}
                    </span>
                    <span wire:loading wire:target="prosesIzin">Memproses...</span>
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
</x-filament-panels::page>
<x-filament-panels::page>
<div class="space-y-4">

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas</label>
                <select wire:model.live="kelas_id"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($this->kelasList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tampilan</label>
                <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                    <button wire:click="$set('mode', 'mingguan')"
                        class="px-4 py-2 text-sm font-medium transition-colors
                        {{ $mode === 'mingguan' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        Mingguan
                    </button>
                    <button wire:click="$set('mode', 'harian')"
                        class="px-4 py-2 text-sm font-medium transition-colors border-l border-gray-300 dark:border-gray-600
                        {{ $mode === 'harian' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        Harian
                    </button>
                </div>
            </div>

            @if($mode === 'harian')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hari</label>
                <select wire:model.live="hari_filter"
                    class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                    @foreach($this->hariList as $hari)
                        <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    @if(!$kelas_id)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-16 text-center">
            <svg class="w-14 h-14 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">Pilih kelas untuk melihat jadwal pelajaran</p>
        </div>

    @else
        @php
            $gridData  = $this->jadwalGrid;
            $grid      = $gridData['grid'];
            $jamList   = $this->jamSlots;
            $hariKolom = $mode === 'mingguan' ? $this->hariList : [$hari_filter];
            $colCount  = count($hariKolom);
            $ROW_H     = 64; // tinggi per slot dalam px
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">

                {{-- CSS Grid container --}}
                <div style="
                    display: grid;
                    grid-template-columns: 72px repeat({{ $colCount }}, minmax(130px, 1fr));
                    grid-template-rows: 44px repeat({{ count($jamList) }}, {{ $ROW_H }}px);
                    min-width: {{ 72 + ($colCount * 130) }}px;
                ">

                    {{-- Corner header --}}
                    <div style="grid-column:1; grid-row:1;"
                        class="flex items-center px-3 bg-gray-50 dark:bg-gray-700 border-b border-r border-gray-200 dark:border-gray-600">
                        <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Jam</span>
                    </div>

                    {{-- Header hari --}}
                    @foreach($hariKolom as $hIdx => $hari)
                    <div style="grid-column:{{ $hIdx + 2 }}; grid-row:1;"
                        class="flex items-center justify-center bg-gray-50 dark:bg-gray-700 border-b border-r border-gray-200 dark:border-gray-600">
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase">{{ $hari }}</span>
                    </div>
                    @endforeach

                    {{-- Jam labels + background cells --}}
                    @foreach($jamList as $jIdx => $jam)

                        {{-- Label jam --}}
                        <div style="grid-column:1; grid-row:{{ $jIdx + 2 }};"
                            class="flex items-start justify-center pt-2 bg-gray-50 dark:bg-gray-700/50 border-b border-r border-gray-200 dark:border-gray-600">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">{{ $jam }}</span>
                        </div>

                        {{-- Background cell per hari --}}
                        @foreach($hariKolom as $hIdx => $hari)
                        <div style="grid-column:{{ $hIdx + 2 }}; grid-row:{{ $jIdx + 2 }};"
                            class="border-b border-r border-gray-100 dark:border-gray-700">
                        </div>
                        @endforeach

                    @endforeach

                    {{-- Events — ditimpa di atas background cells --}}
                    @foreach($hariKolom as $hIdx => $hari)
                        @foreach($jamList as $jIdx => $jam)
                            @if(isset($grid[$hari][$jam]))
                                @php
                                    $item    = $grid[$hari][$jam];
                                    $jadwal  = $item['jadwal'];
                                    $rowspan = $item['rowspan'];
                                    $mapel   = $jadwal->mataPelajaran;
                                    $guru    = $jadwal->guru;
                                    $warna   = $this->warnaKategori($mapel?->kategori);
                                @endphp
                                <div style="
                                    grid-column: {{ $hIdx + 2 }};
                                    grid-row: {{ $jIdx + 2 }} / span {{ $rowspan }};
                                    padding: 4px;
                                    z-index: 2;
                                ">
                                    <div class="rounded-lg {{ $warna }}" style="height: 100%; padding: 8px 10px; display: flex; flex-direction: column; justify-content: center; gap: 2px;">
                                        <p style="font-size: 12px; font-weight: 700; line-height: 1.3; margin: 0;">
                                            {{ $mapel?->pelajaran ?? '-' }}
                                        </p>
                                        @if($guru)
                                        <p style="font-size: 11px; opacity: 0.8; margin: 0;">
                                            {{ $guru->name }}
                                        </p>
                                        @endif
                                        <p style="font-size: 10px; opacity: 0.6; margin: 0;">
                                            {{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach

                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-2 px-1 items-center">
            <span class="text-xs text-gray-400 dark:text-gray-500">Kategori:</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300">Umum</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300">Keagamaan</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200 dark:bg-orange-900/30 dark:text-orange-300">Ekstrakurikuler</span>
        </div>

    @endif

</div>
</x-filament-panels::page>
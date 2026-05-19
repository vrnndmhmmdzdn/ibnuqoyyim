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
                            {{ $mode === 'mingguan' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50' }}">
                        Mingguan
                    </button>
                    <button wire:click="$set('mode', 'harian')"
                        class="px-4 py-2 text-sm font-medium transition-colors border-l border-gray-300 dark:border-gray-600
                            {{ $mode === 'harian' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50' }}">
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

    {{-- Belum pilih kelas --}}
    @if(!$kelas_id)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-16 text-center">
        <svg class="w-14 h-14 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-gray-500 dark:text-gray-400 font-medium">Pilih kelas untuk melihat jadwal pelajaran</p>
    </div>

    @else

    {{-- Grid --}}
    @php
        $gridData  = $this->jadwalGrid;
        $grid      = $gridData['grid'];
        $occupied  = $gridData['occupied'];
        $hariKolom = $mode === 'mingguan' ? $this->hariList : [$hari_filter];
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr>
                        <th class="w-20 px-3 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700/80 border-b border-r border-gray-200 dark:border-gray-600 sticky left-0 z-10">
                            Jam
                        </th>
                        @foreach($hariKolom as $hari)
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase bg-gray-50 dark:bg-gray-700/80 border-b border-r border-gray-200 dark:border-gray-600 min-w-40">
                            {{ $hari }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->jamSlots as $jam)
                    <tr class="border-b border-gray-100 dark:border-gray-700" style="height: 56px">

                        {{-- Kolom Jam --}}
                        <td class="px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 border-r border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 sticky left-0 whitespace-nowrap h-14">
                            {{ $jam }}
                        </td>

                        {{-- Kolom per Hari --}}
                        @foreach($hariKolom as $hari)
                            @if(isset($occupied[$hari][$jam]))
                                {{-- Dicakup rowspan di atas, skip --}}
                            @elseif(isset($grid[$hari][$jam]))
                                @php
                                    $item    = $grid[$hari][$jam];
                                    $jadwal  = $item['jadwal'];
                                    $rowspan = $item['rowspan'];
                                    $mapel   = $jadwal->mataPelajaran;
                                    $guru    = $jadwal->guru;
                                    $warna   = $this->warnaKategori($mapel?->kategori);
                                @endphp
                                <td rowspan="{{ $rowspan }}"
                                    class="border-r border-gray-200 dark:border-gray-600 align-top p-1 relative"
                                    style="min-height: {{ $rowspan * 56 }}px">
                                    <div class="absolute inset-1 rounded-lg flex flex-col justify-center px-3 py-2 {{ $warna }}">
                                        <p class="text-xs font-semibold leading-tight">
                                            {{ $mapel?->pelajaran ?? '-' }}
                                        </p>
                                        @if($guru)
                                        <p class="text-xs opacity-75 mt-0.5 leading-tight">
                                            {{ $guru->name }}
                                        </p>
                                        @endif
                                        <p class="text-xs opacity-50 mt-0.5">
                                            {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                        </p>
                                    </div>
                                </td>
                            @else
                                <td class="px-2 py-2 border-r border-gray-200 dark:border-gray-600 h-14"></td>
                            @endif
                        @endforeach

                    </tr>
                    @endforeach
                </tbody>
            </table>
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
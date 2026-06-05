@php
        use Modules\MutabaahTahfidz\Models\MutabaahRecord;
        use Modules\Kelas\Models\Kelas;
        use Modules\Siswa\Models\Siswa;

        $today        = \Carbon\Carbon::today();
        $totalRecords = MutabaahRecord::whereDate('tanggal', $today)->count();
        $totalSiswa   = Siswa::where('status_siswa', 'aktif')->count();
        $totalKelas   = Kelas::count();
        $mumtazToday  = MutabaahRecord::whereDate('tanggal', $today)->where('nilai', 'mumtaz')->count();
@endphp
<x-filament-panels::page>
<style>
.mt-card { background:#fff; border:0.5px solid #e5e7eb; border-radius:14px; padding:16px; }
.dark .mt-card { background:#1f2937; border-color:#374151; }
.stat-card { border-radius:14px; padding:14px; border-left:4px solid; }
.rec-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:99px; font-size:11px; font-weight:500; }
</style>

<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Global stats --}}

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;@media(min-width:768px){grid-template-columns:repeat(4,1fr)}">
        @foreach([
            ['Setoran Hari Ini', $totalRecords, 'border-l-green-500',  'bg-green-50 dark:bg-green-900/20',  'text-green-600',  'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['Total Santri Aktif', $totalSiswa, 'border-l-blue-500',   'bg-blue-50 dark:bg-blue-900/20',    'text-blue-600',   'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
            ['Total Kelas', $totalKelas,        'border-l-purple-500', 'bg-purple-50 dark:bg-purple-900/20','text-purple-600', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['Mumtaz Hari Ini',  $mumtazToday,  'border-l-amber-500',  'bg-amber-50 dark:bg-amber-900/20',  'text-amber-600',  'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
        ] as [$lbl, $val, $border, $bg, $clr, $path])
        <div class="mt-card" style="border-left:4px solid">
            <div class="{{ $bg }} rounded-xl p-3" style="display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div style="font-size:11px;font-weight:500;color:#6b7280">{{ $lbl }}</div>
                    <div style="font-size:24px;font-weight:700" class="{{ $clr }}">{{ $val }}</div>
                </div>
                <svg class="{{ $clr }}" style="width:28px;height:28px;opacity:.7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                </svg>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Per-class summary --}}
    @php
        $kelasList = Kelas::withCount([
            'siswas as total_siswa' => fn($q) => $q->where('kelas_pivot.is_aktif', true)->whereNull('kelas_pivot.deleted_at'),
        ])->orderBy('nama_kelas')->get();
    @endphp

    <div class="mt-card">
        <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:12px" class="dark:text-white">
            📚 Status Setoran Per Kelas — {{ $today->locale('id')->translatedFormat('l, d F Y') }}
        </div>
        <div style="display:flex;flex-direction:column;gap:2px">
            @forelse ($kelasList as $kelas)
            @php
                $inputToday = MutabaahRecord::where('kelas_id', $kelas->id)
                    ->whereDate('tanggal', $today)->count();
                $total      = $kelas->total_siswa ?? 0;
                $pct        = $total > 0 ? round($inputToday / $total * 100) : 0;
            @endphp
            <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:0.5px solid #f3f4f6" class="dark:border-gray-700">
                <div style="min-width:90px;font-size:13px;font-weight:500;color:#374151" class="dark:text-gray-300">
                    {{ $kelas->nama_kelas }}
                </div>
                <div style="flex:1;background:#f3f4f6;border-radius:99px;height:8px;overflow:hidden" class="dark:bg-gray-700">
                    <div style="width:{{ $pct }}%;height:100%;background:#16a34a;border-radius:99px;transition:width .4s"></div>
                </div>
                <div style="font-size:12px;font-weight:600;color:#15803d;min-width:70px;text-align:right">
                    {{ $inputToday }}/{{ $total }}
                    <span style="font-weight:400;color:#9ca3af">({{ $pct }}%)</span>
                </div>
                <a href="{{ route('filament.admin.pages.mutabaah-input') }}"
                    style="font-size:11px;color:#16a34a;font-weight:500">
                    Input →
                </a>
            </div>
            @empty
                <p style="text-align:center;color:#9ca3af;font-size:13px;padding:20px">
                    Belum ada kelas. <a href="{{ route('filament.admin.pages.mutabaah-kelas-setup') }}" class="text-green-600">Setup kelas →</a>
                </p>
            @endforelse
        </div>
    </div>

    {{-- Recent records --}}
    @php
        $recentRecords = MutabaahRecord::with(['siswa', 'kelas', 'surah', 'guru'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    @endphp

    <div class="mt-card">
        <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:12px" class="dark:text-white">🕐 Setoran Terbaru</div>
        <div style="display:flex;flex-direction:column">
            @forelse ($recentRecords as $rec)
            <div style="display:flex;align-items:start;gap:10px;padding:10px 0;border-bottom:0.5px solid #f3f4f6" class="dark:border-gray-700">
                <div style="width:32px;height:32px;border-radius:50%;background:#dcfce7;color:#15803d;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">
                    {{ strtoupper(substr($rec->siswa?->nama_lengkap ?? '?', 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                        <span style="font-size:13px;font-weight:600;color:#111827" class="dark:text-white">{{ $rec->siswa?->nama_lengkap }}</span>
                        <span style="font-size:11px;color:#9ca3af">·</span>
                        <span style="font-size:12px;color:#6b7280">{{ $rec->kelas?->nama_kelas }}</span>
                    </div>
                    <div style="font-size:12px;color:#6b7280;margin-top:2px">
                        @if ($rec->surah)
                            {{ $rec->surah->nama_surah }} : {{ $rec->ayat_awal }}–{{ $rec->ayat_akhir }}
                            ({{ $rec->jumlah_ayat }} ayat)
                        @endif
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;flex-shrink:0">
                    <span class="rec-badge {{ MutabaahRecord::statusClass($rec->status) }}">
                        {{ MutabaahRecord::statusEmoji($rec->status) }}
                        {{ MutabaahRecord::STATUS[$rec->status] }}
                    </span>
                    @if ($rec->nilai)
                        <span class="rec-badge {{ MutabaahRecord::nilaiClass($rec->nilai) }}">
                            {{ MutabaahRecord::nilaiEmoji($rec->nilai) }}
                            {{ MutabaahRecord::NILAI[$rec->nilai] }}
                        </span>
                    @endif
                    <span style="font-size:10px;color:#9ca3af">{{ $rec->tanggal->locale('id')->translatedFormat('d M Y') }}</span>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px">Belum ada data setoran.</div>
            @endforelse
        </div>
    </div>

</div>
</x-filament-panels::page>
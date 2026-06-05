<x-filament-panels::page>
<style>
.mt-card { background:#fff; border:0.5px solid #e5e7eb; border-radius:14px; padding:16px; }
.dark .mt-card { background:#1f2937; border-color:#374151; }
.mt-label { font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:#6b7280; margin-bottom:8px; }
.dark .mt-label { color:#9ca3af; }
.mt-select { width:100%; padding:9px 12px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:13px; background:#fff; color:#111827; outline:none; }
.dark .mt-select { background:#111827; border-color:#374151; color:#f3f4f6; }

/* Periode tabs */
.p-tab { padding:6px 16px; border-radius:99px; font-size:12px; font-weight:500; cursor:pointer; border:1.5px solid #e5e7eb; background:#f9fafb; color:#374151; transition:all .15s; }
.dark .p-tab { background:#111827; border-color:#374151; color:#d1d5db; }
.p-tab.active { background:#16a34a; border-color:#16a34a; color:#fff; }

/* Podium */
.podium { display:flex; align-items:flex-end; justify-content:center; gap:12px; padding:20px 0 8px; }
.podium-col { display:flex; flex-direction:column; align-items:center; gap:6px; }
.podium-bar { border-radius:8px 8px 0 0; width:72px; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; padding-bottom:8px; min-height:40px; }
.podium-bar-1 { background:linear-gradient(to top,#f59e0b,#fbbf24); height:120px; }
.podium-bar-2 { background:linear-gradient(to top,#6b7280,#9ca3af); height:90px; }
.podium-bar-3 { background:linear-gradient(to top,#92400e,#b45309); height:70px; }
.podium-rank { font-size:22px; }
.podium-name { font-size:12px; font-weight:600; text-align:center; max-width:80px; color:#111827; }
.dark .podium-name { color:#f3f4f6; }
.podium-ayat { font-size:11px; color:#6b7280; }
.dark .podium-ayat { color:#9ca3af; }

/* Progress bars */
.prog-row { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
.prog-name { font-size:12px; font-weight:500; color:#111827; min-width:120px; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.dark .prog-name { color:#f3f4f6; }
.prog-bar-wrap { flex:1; background:#f3f4f6; border-radius:99px; height:10px; overflow:hidden; }
.dark .prog-bar-wrap { background:#374151; }
.prog-bar-fill { height:100%; border-radius:99px; background:#16a34a; transition:width .4s; }
.prog-val { font-size:12px; font-weight:600; color:#15803d; min-width:40px; text-align:right; }

/* Nilai badge */
.nbadge { display:inline-flex; align-items:center; padding:2px 7px; border-radius:99px; font-size:11px; font-weight:500; margin:1px; }

/* Alert box */
.alert-box { padding:10px 14px; border-radius:10px; background:#fef2f2; border:1px solid #fecaca; }
.dark .alert-box { background:#1f0808; border-color:#7f1d1d; }
.alert-item { display:flex; align-items:center; gap:8px; padding:6px 0; border-bottom:0.5px solid #fecaca; }
.dark .alert-item { border-color:#7f1d1d; }
.alert-item:last-child { border-bottom:none; }

/* Stats overview */
.stat-mini { background:#f0fdf4; border-radius:10px; padding:12px; text-align:center; }
.stat-mini-val { font-size:22px; font-weight:700; color:#15803d; }
.stat-mini-lbl { font-size:11px; color:#16a34a; margin-top:2px; }
.dark .stat-mini { background:#052e16; }
.dark .stat-mini-val { color:#4ade80; }
.dark .stat-mini-lbl { color:#86efac; }
</style>

<div style="display:flex;flex-direction:column;gap:14px">

    {{-- Controls --}}
    <div class="mt-card">
        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end">
            <div style="flex:1;min-width:160px">
                <div class="mt-label">Kelas</div>
                <select wire:model.live="kelas_id" class="mt-select">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($this->kelasList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <div class="mt-label">Periode</div>
                <div style="display:flex;gap:6px">
                    @foreach(['minggu' => 'Minggu Ini', 'bulan' => 'Bulan Ini', 'semua' => 'Semua'] as $val => $lbl)
                        <button wire:click="$set('periode','{{ $val }}')"
                            class="p-tab {{ $periode === $val ? 'active' : '' }}">{{ $lbl }}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if (!$kelas_id)
        <div class="mt-card" style="text-align:center;padding:48px;color:#9ca3af">
            <div style="font-size:40px;margin-bottom:12px">🏆</div>
            <p style="font-size:14px;font-weight:500">Pilih kelas untuk melihat statistik</p>
        </div>
    @else
        @php $sd = $this->statsData; $rows = $sd['rows'] ?? []; @endphp

        {{-- Overview stats --}}
        @if (!empty($sd))
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
            @foreach([
                ['Total Ayat', $sd['total']['ayat'] ?? 0],
                ['Total Setor', $sd['total']['setor'] ?? 0],
                ['Mumtaz', $sd['total']['mumtaz'] ?? 0],
                ['Santri Aktif', $sd['total']['siswa'] ?? 0],
            ] as [$lbl, $val])
            <div class="stat-mini">
                <div class="stat-mini-val">{{ $val }}</div>
                <div class="stat-mini-lbl">{{ $lbl }}</div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Podium Top 3 --}}
        @if (!empty($sd['podium']))
        <div class="mt-card">
            <div class="mt-label">🏆 Top Hafalan</div>
            <div class="podium">
                @php
                    // Reorder: 2nd, 1st, 3rd for visual podium display
                    $podium = $sd['podium'];
                    $order  = [
                        1 => $podium[1] ?? null,
                        0 => $podium[0] ?? null,
                        2 => $podium[2] ?? null,
                    ];
                    $bars    = ['podium-bar-2','podium-bar-1','podium-bar-3'];
                    $ranks   = ['🥈','🥇','🥉'];
                    $bKeys   = [1, 0, 2];
                @endphp
                @foreach ($bKeys as $bi => $ri)
                    @if (!empty($podium[$ri]))
                    @php $p = $podium[$ri]; @endphp
                    <div class="podium-col">
                        <div class="podium-name">{{ $p['siswa']->nama_lengkap }}</div>
                        <div class="podium-ayat">{{ $p['totalAyat'] }} ayat</div>
                        <div class="podium-bar {{ $bars[$bi] }}">
                            <span class="podium-rank">{{ $ranks[$bi] }}</span>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Progress all students --}}
        @if (!empty($rows))
        <div class="mt-card">
            <div class="mt-label">📊 Progress Hafalan Semua Santri</div>
            @php $maxAyat = max(1, $sd['maxAyat']); @endphp
            @foreach ($rows as $i => $row)
            <div class="prog-row">
                <div class="prog-name" title="{{ $row['siswa']->nama_lengkap }}">
                    {{ $i + 1 }}. {{ $row['siswa']->nama_lengkap }}
                </div>
                <div class="prog-bar-wrap">
                    <div class="prog-bar-fill"
                        style="width:{{ $maxAyat > 0 ? min(100, round($row['totalAyat'] / $maxAyat * 100)) : 0 }}%;
                               background:{{ $row['totalAyat'] === 0 ? '#e5e7eb' : '#16a34a' }}">
                    </div>
                </div>
                <div class="prog-val">{{ $row['totalAyat'] }}</div>
                <div style="min-width:80px;text-align:right">
                    @if ($row['mumtaz'] > 0)
                        <span class="nbadge" style="background:#dcfce7;color:#15803d">⭐{{ $row['mumtaz'] }}</span>
                    @endif
                    @if ($row['rasib'] > 0)
                        <span class="nbadge" style="background:#fee2e2;color:#b91c1c">😞{{ $row['rasib'] }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Per-student detail cards --}}
        @if (!empty($rows))
        <div class="mt-card">
            <div class="mt-label">📋 Detail Per Santri</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                @foreach ($rows as $row)
                <div style="border:0.5px solid #e5e7eb;border-radius:10px;padding:10px" class="dark:border-gray-700">
                    <div style="font-size:13px;font-weight:600;color:#111827;margin-bottom:6px" class="dark:text-white">
                        {{ $row['siswa']->nama_lengkap }}
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;font-size:11px;color:#6b7280">
                        <span>📖 {{ $row['totalAyat'] }} ayat</span>
                        <span>✅ {{ $row['hariSetor'] }} setor</span>
                        <span>⭐ {{ $row['mumtaz'] }} mumtaz</span>
                        <span>❌ {{ $row['tidakSetor'] }} tdk setor</span>
                    </div>
                    @if ($row['lastRecord'])
                    <div style="margin-top:6px;font-size:11px;color:#9ca3af">
                        Terakhir:
                        @if ($row['lastRecord']->surah)
                            {{ $row['lastRecord']->surah->nama_surah }}
                            {{ $row['lastRecord']->ayat_awal }}–{{ $row['lastRecord']->ayat_akhir }}
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Alert: students with 0 setoran --}}
        @if (!empty($sd['alerts']))
        <div class="alert-box">
            <div style="font-size:13px;font-weight:600;color:#b91c1c;margin-bottom:8px">
                ⚠️ {{ count($sd['alerts']) }} Santri Belum Setoran
            </div>
            @foreach ($sd['alerts'] as $a)
            <div class="alert-item">
                <div style="width:28px;height:28px;border-radius:50%;background:#fee2e2;color:#b91c1c;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">
                    {{ strtoupper(substr($a['siswa']->nama_lengkap, 0, 1)) }}
                </div>
                <div style="font-size:12px;color:#b91c1c;font-weight:500">{{ $a['siswa']->nama_lengkap }}</div>
            </div>
            @endforeach
        </div>
        @endif

    @endif
</div>
</x-filament-panels::page>
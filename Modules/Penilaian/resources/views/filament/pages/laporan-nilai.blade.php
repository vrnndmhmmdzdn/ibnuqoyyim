<x-filament-panels::page>
<style>
.mt-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; }
.dark .mt-card { background:#1f2937; border-color:#374151; }
.mt-label { font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:#6b7280; margin-bottom:4px; }
.dark .mt-label { color:#9ca3af; }
.mt-select { width:100%; padding:8px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:13px; background:#fff; color:#111827; outline:none; }
.mt-select:focus { border-color:#16a34a; }
.dark .mt-select { background:#111827; border-color:#374151; color:#f3f4f6; }

/* Mode tabs */
.mode-tab { padding:6px 16px; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; border:1.5px solid #e5e7eb; background:#f9fafb; color:#374151; transition:all .15s; }
.dark .mode-tab { background:#111827; border-color:#374151; color:#d1d5db; }
.mode-tab.active { background:#16a34a; border-color:#16a34a; color:#fff; }

/* Predikat */
.predikat-a { background:#dcfce7; color:#15803d; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; }
.predikat-b { background:#dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; }
.predikat-c { background:#fef9c3; color:#a16207; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; }
.predikat-d { background:#fee2e2; color:#b91c1c; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; }

/* Bar chart */
.bar-wrap { background:#f3f4f6; border-radius:4px; height:8px; overflow:hidden; flex:1; }
.dark .bar-wrap { background:#374151; }
.bar-fill-green  { height:100%; background:#16a34a; border-radius:4px; transition:width .3s; }
.bar-fill-blue   { height:100%; background:#3b82f6; border-radius:4px; transition:width .3s; }
.bar-fill-amber  { height:100%; background:#f59e0b; border-radius:4px; transition:width .3s; }
.bar-fill-purple { height:100%; background:#8b5cf6; border-radius:4px; transition:width .3s; }

/* Export btn */
.btn-export { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; border:1.5px solid; transition:all .15s; }
.btn-wa  { background:#25d366; border-color:#25d366; color:#fff; }
.btn-wa:hover { background:#1da851; }
.btn-outline { background:#fff; border-color:#e5e7eb; color:#374151; }
.dark .btn-outline { background:#1f2937; border-color:#374151; color:#d1d5db; }
.btn-outline:hover { border-color:#16a34a; color:#16a34a; }

/* Rank badge */
.rank-1 { background:#fef9c3; color:#a16207; border:1.5px solid #fde047; }
.rank-2 { background:#f3f4f6; color:#374151; border:1.5px solid #d1d5db; }
.rank-3 { background:#ffedd5; color:#c2410c; border:1.5px solid #fdba74; }

/* Mapel card (mode siswa) */
.mapel-card { border:1px solid #e5e7eb; border-radius:10px; padding:14px; }
.dark .mapel-card { border-color:#374151; }
</style>

<div style="display:flex; flex-direction:column; gap:14px;">

    {{-- ── Filter + Mode ──────────────────────────────────────────── --}}
    <div class="mt-card">
        <div style="display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px;">
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:10px; flex:1;">
                <div>
                    <div class="mt-label">Kelas</div>
                    <select wire:model.live="kelas_id" class="mt-select">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($this->kelasList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="mt-label">Tahun Ajaran</div>
                    <select wire:model.live="tahun_ajaran_id" class="mt-select">
                        @foreach($this->tahunAjaranList as $id => $ta)
                            <option value="{{ $id }}">{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="mt-label">Semester</div>
                    <select wire:model.live="semester" class="mt-select">
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
            </div>

            {{-- Mode toggle --}}
            <div>
                <div class="mt-label">Tampilan</div>
                <div style="display:flex; gap:6px;">
                    <button wire:click="$set('mode','kelas')"
                        class="mode-tab {{ $mode === 'kelas' ? 'active' : '' }}">📊 Per Kelas</button>
                    <button wire:click="$set('mode','siswa')"
                        class="mode-tab {{ $mode === 'siswa' ? 'active' : '' }}">👤 Per Siswa</button>
                </div>
            </div>
        </div>
    </div>

    @if(!$kelas_id || !$tahun_ajaran_id)
    <div class="mt-card" style="text-align:center; padding:48px; color:#9ca3af;">
        <div style="font-size:40px; margin-bottom:12px;">📋</div>
        <p style="font-size:14px; font-weight:500;">Pilih kelas dan tahun ajaran untuk melihat laporan</p>
    </div>

    {{-- ══════════════ MODE: PER KELAS ══════════════ --}}
    @elseif($mode === 'kelas')

    {{-- Summary chips --}}
    @php
        $ranking = $this->rankingSiswa;
        $mapels  = $this->mapelList;
    @endphp

    @if($ranking->isNotEmpty())
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px;">
        <div style="background:#dcfce7; border-radius:10px; padding:12px; text-align:center;">
            <div style="font-size:22px; font-weight:700; color:#15803d;">{{ $ranking->count() }}</div>
            <div style="font-size:11px; color:#16a34a; margin-top:2px;">Siswa</div>
        </div>
        <div style="background:#dbeafe; border-radius:10px; padding:12px; text-align:center;">
            <div style="font-size:22px; font-weight:700; color:#1d4ed8;">{{ $mapels->count() }}</div>
            <div style="font-size:11px; color:#2563eb; margin-top:2px;">Mata Pelajaran</div>
        </div>
        <div style="background:#ede9fe; border-radius:10px; padding:12px; text-align:center;">
            <div style="font-size:22px; font-weight:700; color:#6d28d9;">
                {{ $ranking->whereNotNull('rata_rata')->avg('rata_rata') ? number_format($ranking->whereNotNull('rata_rata')->avg('rata_rata'), 1) : '—' }}
            </div>
            <div style="font-size:11px; color:#7c3aed; margin-top:2px;">Rata-rata Kelas</div>
        </div>
    </div>
    @endif

    {{-- Tabel ranking per kelas --}}
    @if($this->siswaList->isEmpty())
    <div class="mt-card" style="text-align:center; padding:40px; color:#9ca3af;">
        <p style="font-size:13px;">Tidak ada siswa di kelas ini.</p>
    </div>
    @elseif($mapels->isEmpty())
    <div class="mt-card" style="text-align:center; padding:40px; color:#9ca3af;">
        <p style="font-size:13px;">Belum ada data penilaian untuk semester ini.</p>
    </div>
    @else
    <div class="mt-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13px; min-width:500px;">
                <thead>
                    <tr style="background:#f0fdf4;">
                        <th style="padding:10px 10px; text-align:center; font-size:11px; font-weight:600; color:#15803d; border-bottom:2px solid #86efac; width:48px;">#</th>
                        <th style="padding:10px 12px; text-align:left; font-size:11px; font-weight:600; color:#15803d; border-bottom:2px solid #86efac; min-width:160px;">Nama Siswa</th>
                        @foreach($mapels as $mapel)
                        <th style="padding:10px 8px; text-align:center; font-size:11px; font-weight:600; color:#15803d; border-bottom:2px solid #86efac; min-width:80px; white-space:nowrap;">
                            {{ $mapel->pelajaran }}
                        </th>
                        @endforeach
                        <th style="padding:10px 10px; text-align:center; font-size:11px; font-weight:600; color:#15803d; border-bottom:2px solid #86efac; min-width:70px;">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $i => $row)
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        {{-- Rank badge --}}
                        <td style="padding:9px 10px; text-align:center;">
                            @if($i < 3 && $row->rata_rata !== null)
                                <span style="display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; font-size:11px; font-weight:700;"
                                    class="rank-{{ $i+1 }}">
                                    {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : '🥉') }}
                                </span>
                            @else
                                <span style="color:#d1d5db; font-size:12px;">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td style="padding:9px 12px; font-weight:500; color:#111827;" class="dark:text-white">
                            {{ $row->siswa->nama_lengkap }}
                            <div style="font-size:11px; color:#9ca3af;">{{ $row->siswa->nis }}</div>
                        </td>
                        @foreach($mapels as $mapel)
                        @php $rekap = $this->rekapData[$row->siswa->id][$mapel->id] ?? null; @endphp
                        <td style="padding:9px 8px; text-align:center;">
                            @if($rekap && $rekap->nilai_akhir !== null)
                                <div style="font-weight:700; color:#111827; font-size:14px;" class="dark:text-white">
                                    {{ number_format($rekap->nilai_akhir, 1) }}
                                </div>
                                @if($rekap->predikat)
                                    <span class="predikat-{{ strtolower($rekap->predikat) }}">{{ $rekap->predikat }}</span>
                                @endif
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        @endforeach
                        <td style="padding:9px 10px; text-align:center;">
                            @if($row->rata_rata !== null)
                                <span style="font-weight:700; font-size:15px; color:#16a34a;">{{ $row->rata_rata }}</span>
                                <div style="margin-top:2px;">
                                    @if($row->jumlah_a > 0)<span class="predikat-a" style="font-size:10px;">{{ $row->jumlah_a }}A</span>@endif
                                    @if($row->jumlah_b > 0)<span class="predikat-b" style="font-size:10px;">{{ $row->jumlah_b }}B</span>@endif
                                </div>
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Legend --}}
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center; font-size:12px; color:#6b7280;">
        <span>Predikat:</span>
        <span class="predikat-a">A ≥ 90</span>
        <span class="predikat-b">B 75–89</span>
        <span class="predikat-c">C 60–74</span>
        <span class="predikat-d">D &lt; 60</span>
    </div>
    @endif

    {{-- ══════════════ MODE: PER SISWA ══════════════ --}}
    @else

    {{-- Siswa selector + WA button --}}
    <div class="mt-card">
        <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
            <div style="flex:1; min-width:200px;">
                <div class="mt-label">Pilih Siswa</div>
                <select wire:model.live="selected_siswa_id" class="mt-select">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($this->siswaList as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            @if($selected_siswa_id)
            <button class="btn-export btn-wa"
                x-on:click="$wire.generateWaText().then(t => {
                    navigator.clipboard.writeText(t).then(() => {
                        let b = $el;
                        let orig = b.innerHTML;
                        b.innerHTML = '✅ Tersalin!';
                        setTimeout(() => b.innerHTML = orig, 2000);
                    });
                })">
                <svg style="width:15px;height:15px;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Salin ke WA
            </button>
            @endif
        </div>
    </div>

    @if(!$selected_siswa_id)
    <div class="mt-card" style="text-align:center; padding:48px; color:#9ca3af;">
        <div style="font-size:40px; margin-bottom:12px;">👤</div>
        <p style="font-size:14px; font-weight:500;">Pilih siswa untuk melihat detail nilai</p>
    </div>
    @elseif($this->dataSiswa->isEmpty())
    <div class="mt-card" style="text-align:center; padding:40px; color:#9ca3af;">
        <p style="font-size:13px;">Belum ada rekap nilai untuk siswa ini.</p>
    </div>
    @else

    {{-- Ringkasan atas --}}
    @php
        $nilaiAll = $this->dataSiswa->pluck('nilai_akhir')->filter();
        $avgAll   = $nilaiAll->isNotEmpty() ? round($nilaiAll->avg(), 1) : null;
        $jmlA     = $this->dataSiswa->where('predikat','A')->count();
        $jmlB     = $this->dataSiswa->where('predikat','B')->count();
        $jmlC     = $this->dataSiswa->where('predikat','C')->count();
        $jmlD     = $this->dataSiswa->where('predikat','D')->count();
    @endphp
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px;">
        <div style="background:#f0fdf4; border-radius:10px; padding:14px; text-align:center; border:1px solid #bbf7d0;">
            <div style="font-size:24px; font-weight:700; color:#15803d;">{{ $avgAll ?? '—' }}</div>
            <div style="font-size:11px; color:#16a34a; margin-top:2px;">Rata-rata</div>
        </div>
        <div style="background:#dcfce7; border-radius:10px; padding:14px; text-align:center;">
            <div style="font-size:24px; font-weight:700; color:#15803d;">{{ $jmlA }}</div>
            <div style="font-size:11px; color:#16a34a;">Predikat A</div>
        </div>
        <div style="background:#dbeafe; border-radius:10px; padding:14px; text-align:center;">
            <div style="font-size:24px; font-weight:700; color:#1d4ed8;">{{ $jmlB }}</div>
            <div style="font-size:11px; color:#2563eb;">Predikat B</div>
        </div>
        <div style="background:#fef9c3; border-radius:10px; padding:14px; text-align:center;">
            <div style="font-size:24px; font-weight:700; color:#a16207;">{{ $jmlC + $jmlD }}</div>
            <div style="font-size:11px; color:#ca8a04;">Perlu Perhatian</div>
        </div>
    </div>

    {{-- Detail per mapel --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:12px;">
        @foreach($this->dataSiswa as $rekap)
        @php
            $na = $rekap->nilai_akhir;
            $pr = $rekap->predikat;
            $barNH  = $rekap->rata_harian ?? 0;
            $barNT  = $rekap->rata_tugas  ?? 0;
            $barPTS = $rekap->nilai_pts   ?? 0;
            $barPAS = $rekap->nilai_pas   ?? 0;
        @endphp
        <div class="mapel-card">
            {{-- Header mapel --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <div>
                    <div style="font-size:14px; font-weight:700; color:#111827;" class="dark:text-white">
                        {{ $rekap->mataPelajaran?->pelajaran ?? '—' }}
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    @if($na !== null)
                        <span style="font-size:22px; font-weight:700; color:#16a34a;">{{ number_format($na, 1) }}</span>
                    @endif
                    @if($pr)
                        <span class="predikat-{{ strtolower($pr) }}">{{ $pr }}</span>
                    @endif
                </div>
            </div>

            {{-- Breakdown bars --}}
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach([
                    ['NH', $barNH,  'bar-fill-green',  '#16a34a'],
                    ['NT', $barNT,  'bar-fill-blue',   '#3b82f6'],
                    ['PTS',$barPTS, 'bar-fill-amber',  '#f59e0b'],
                    ['PAS',$barPAS, 'bar-fill-purple', '#8b5cf6'],
                ] as [$lbl, $val, $cls, $clr])
                <div style="display:flex; align-items:center; gap:8px; font-size:12px;">
                    <span style="width:30px; color:#6b7280; font-weight:600;">{{ $lbl }}</span>
                    <div class="bar-wrap">
                        <div class="{{ $cls }}" style="width:{{ min(100, $val) }}%;"></div>
                    </div>
                    <span style="width:36px; text-align:right; font-weight:600; color:{{ $clr }};">
                        {{ $val ? number_format($val, 1) : '—' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif
    @endif

</div>
</x-filament-panels::page>

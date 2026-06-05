<x-filament-panels::page>
<style>
.mt-card { background:#fff; border:0.5px solid #e5e7eb; border-radius:14px; padding:16px; }
.dark .mt-card { background:#1f2937; border-color:#374151; }
.mt-label { font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:#6b7280; }
.dark .mt-label { color:#9ca3af; }
.mt-select { width:100%; padding:9px 12px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:13px; background:#fff; color:#111827; outline:none; }
.mt-select:focus { border-color:#16a34a; }
.dark .mt-select { background:#111827; border-color:#374151; color:#f3f4f6; }

/* Mode toggle */
.mode-tab { padding:7px 18px; border-radius:10px; font-size:13px; font-weight:500; cursor:pointer; border:1.5px solid #e5e7eb; background:#f9fafb; color:#374151; transition:all .15s; }
.dark .mode-tab { background:#111827; border-color:#374151; color:#d1d5db; }
.mode-tab.active { background:#16a34a; border-color:#16a34a; color:#fff; }

/* Week/Month nav */
.nav-btn { padding:7px 14px; border:1.5px solid #e5e7eb; border-radius:10px; background:#f9fafb; font-size:12px; font-weight:500; cursor:pointer; color:#374151; transition:all .15s; }
.dark .nav-btn { background:#111827; border-color:#374151; color:#d1d5db; }
.nav-btn:hover { border-color:#16a34a; }
.nav-label { font-size:14px; font-weight:600; color:#111827; flex:1; text-align:center; }
.dark .nav-label { color:#f9fafb; }

/* Summary chips */
.sum-chip { border-radius:10px; padding:10px; text-align:center; }
.sum-chip-val { font-size:20px; font-weight:700; }
.sum-chip-lbl { font-size:10px; margin-top:2px; }

/* Export buttons */
.export-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:10px; font-size:12px; font-weight:500; cursor:pointer; border:1.5px solid; transition:all .15s; }
.btn-wa  { background:#25d366; border-color:#25d366; color:#fff; }
.btn-wa:hover { background:#1da851; }
.btn-csv { background:#fff; border-color:#e5e7eb; color:#374151; }
.dark .btn-csv { background:#1f2937; border-color:#374151; color:#d1d5db; }
.btn-csv:hover { border-color:#16a34a; color:#16a34a; }

/* Tables */
.lap-table { width:100%; border-collapse:collapse; font-size:12px; }
.lap-table th, .lap-table td { padding:7px 4px; text-align:center; border-bottom:0.5px solid #f3f4f6; }
.dark .lap-table th, .dark .lap-table td { border-color:#374151; }
.lap-table th { background:#f9fafb; font-weight:600; color:#6b7280; font-size:10px; text-transform:uppercase; white-space:nowrap; }
.dark .lap-table th { background:#111827; color:#9ca3af; }
.lap-table td.name-cell { text-align:left; font-weight:500; color:#111827; padding-left:10px; min-width:110px; white-space:nowrap; }
.dark .lap-table td.name-cell { color:#f3f4f6; }
.lap-table tr:hover td { background:#f9fafb; }
.dark .lap-table tr:hover td { background:#111827; }
.lap-table tfoot td { background:#f0fdf4; font-weight:700; color:#15803d; border-top:1.5px solid #bbf7d0; }
.dark .lap-table tfoot td { background:#052e16; color:#4ade80; border-top-color:#166534; }

/* Status cell */
.sc { display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%; font-size:10px; font-weight:700; }
.sc-lanjut         { background:#dcfce7; color:#15803d; }
.sc-ulang          { background:#ffedd5; color:#c2410c; }
.sc-membaca        { background:#dbeafe; color:#1d4ed8; }
.sc-tasmi          { background:#ede9fe; color:#6d28d9; }
.sc-tidak_setoran  { background:#fee2e2; color:#b91c1c; }
.sc-tidak_masuk    { background:#f3f4f6; color:#6b7280; }
.dark .sc-tidak_masuk { background:#374151; }

/* Weekend col highlight */
.weekend-col { background:#fafaf5; }
.dark .weekend-col { background:#1a1f0e; }
</style>

<div style="display:flex;flex-direction:column;gap:14px">

    {{-- ── Controls ──────────────────────────────────────────────── --}}
    <div class="mt-card">
        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end">

            {{-- Kelas --}}
            <div style="flex:1;min-width:150px">
                <div class="mt-label" style="margin-bottom:4px">Kelas</div>
                <select wire:model.live="kelas_id" class="mt-select">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($this->kelasList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mode toggle --}}
            <div>
                <div class="mt-label" style="margin-bottom:4px">Tampilan</div>
                <div style="display:flex;gap:6px">
                    <button wire:click="$set('mode','pekanan')" class="mode-tab {{ $mode === 'pekanan' ? 'active' : '' }}">📅 Pekanan</button>
                    <button wire:click="$set('mode','bulanan')" class="mode-tab {{ $mode === 'bulanan' ? 'active' : '' }}">📆 Bulanan</button>
                </div>
            </div>

            {{-- Navigator: week or month --}}
            @if ($mode === 'pekanan')
            <div style="display:flex;align-items:center;gap:6px;flex:1;min-width:200px">
                <button wire:click="shiftWeek(-1)" class="nav-btn">← Prev</button>
                <span class="nav-label">{{ $this->getWeekLabel() }}</span>
                <button wire:click="shiftWeek(1)" class="nav-btn">Next →</button>
            </div>
            @else
            <div style="display:flex;align-items:center;gap:6px;flex:1;min-width:200px">
                <button wire:click="shiftMonth(-1)" class="nav-btn">← Prev</button>
                <div style="flex:1;display:flex;gap:6px">
                    <select wire:model.live="bulan" class="mt-select" style="flex:1">
                        @foreach(\Modules\MutabaahTahfidz\Filament\Pages\MutabaahLaporan::BULAN_LABEL as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="tahun" class="mt-select" style="width:80px">
                        @foreach($this->tahunList as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="shiftMonth(1)" class="nav-btn">Next →</button>
            </div>
            @endif
        </div>

        {{-- Summary chips --}}
        @php
            $d = $mode === 'pekanan' ? $this->weekData : $this->monthData;
        @endphp
        @if ($kelas_id && !empty($d))
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:12px">
            <div class="sum-chip" style="background:#dcfce7">
                <div class="sum-chip-val" style="color:#15803d">{{ $d['totalAyat'] }}</div>
                <div class="sum-chip-lbl" style="color:#16a34a">Total Ayat</div>
            </div>
            <div class="sum-chip" style="background:#dbeafe">
                <div class="sum-chip-val" style="color:#1d4ed8">{{ $d['totalSetor'] }}</div>
                <div class="sum-chip-lbl" style="color:#2563eb">Hari Setoran</div>
            </div>
            <div class="sum-chip" style="background:#ede9fe">
                <div class="sum-chip-val" style="color:#6d28d9">{{ $d['totalSiswa'] }}</div>
                <div class="sum-chip-lbl" style="color:#7c3aed">Santri Aktif</div>
            </div>
        </div>
        @endif
    </div>

    @if (!$kelas_id)
    <div class="mt-card" style="text-align:center;padding:40px;color:#9ca3af">
        <p style="font-size:14px;font-weight:500">Pilih kelas untuk melihat laporan</p>
    </div>
    @else

    {{-- ── Export buttons ─────────────────────────────────────────── --}}
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        @if ($mode === 'pekanan')
        <button onclick="copyWAPekanan()" class="export-btn btn-wa">
            <svg style="width:15px;height:15px" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Salin WA
        </button>
        <button wire:click="exportCsv" class="export-btn btn-csv">
            <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </button>
        @else
        <button onclick="copyWABulanan()" class="export-btn btn-wa">
            <svg style="width:15px;height:15px" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Salin WA Bulanan
        </button>
        <button wire:click="exportCsvBulanan" class="export-btn btn-csv">
            <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV Bulanan
        </button>
        @endif
    </div>

    {{-- ══════════════════ TABEL PEKANAN ══════════════════ --}}
    @if ($mode === 'pekanan')
    @php $wd = $this->weekData; @endphp
    @if (!empty($wd['rows']))
    <div class="mt-card" style="padding:0;overflow-x:auto">
        <table class="lap-table">
            <thead>
                <tr>
                    <th style="text-align:left;padding-left:10px">Nama</th>
                    @foreach(\Modules\MutabaahTahfidz\Filament\Pages\MutabaahLaporan::HARI_LABEL as $i => $h)
                    @php $date = $wd['dates'][$i]; $isWe = in_array(Carbon\Carbon::parse($date)->dayOfWeek, [0,5,6]); @endphp
                    <th class="{{ $isWe ? 'weekend-col' : '' }}">
                        {{ $h }}<br>
                        <span style="font-weight:400;opacity:.7">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</span>
                    </th>
                    @endforeach
                    <th>Ayat</th>
                    <th>Setor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wd['rows'] as $row)
                <tr>
                    <td class="name-cell">{{ $row['siswa']->nama_lengkap }}</td>
                    @foreach ($wd['dates'] as $i => $date)
                    @php
                        $rec   = $row['dayData'][$date] ?? null;
                        $isWe  = in_array(\Carbon\Carbon::parse($date)->dayOfWeek, [0,5,6]);
                    @endphp
                    <td class="{{ $isWe ? 'weekend-col' : '' }}">
                        @if ($rec)
                            <span class="sc sc-{{ $rec->status }}"
                                title="{{ \Modules\MutabaahTahfidz\Models\MutabaahRecord::STATUS[$rec->status] }}">
                                {{ \Modules\MutabaahTahfidz\Models\MutabaahRecord::statusEmoji($rec->status) }}
                            </span>
                            @if ($rec->jumlah_ayat > 0)
                            <div style="font-size:10px;color:#6b7280;margin-top:1px">{{ $rec->jumlah_ayat }}A</div>
                            @endif
                        @else
                            <span style="color:#e5e7eb">·</span>
                        @endif
                    </td>
                    @endforeach
                    <td style="font-weight:600;color:#15803d">{{ $row['totalAyat'] }}</td>
                    <td style="color:#6b7280">{{ $row['setoran'] }}/7</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align:left;padding-left:10px">Total</td>
                    @foreach ($wd['dates'] as $date)
                    @php $dt = collect($wd['rows'])->sum(fn($r) => $r['dayData'][$date]?->jumlah_ayat ?? 0); @endphp
                    <td>{{ $dt ?: '–' }}</td>
                    @endforeach
                    <td>{{ $wd['totalAyat'] }}</td>
                    <td>{{ $wd['totalSetor'] }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="mt-card" style="text-align:center;padding:32px;color:#9ca3af">
        <p style="font-size:13px">Belum ada data setoran untuk minggu ini.</p>
    </div>
    @endif

    {{-- ══════════════════ TABEL BULANAN ══════════════════ --}}
    @else
    @php $md = $this->monthData; @endphp
    @if (!empty($md['rows']))
    <div class="mt-card" style="padding:0;overflow-x:auto">
        <table class="lap-table" style="min-width:{{ 120 + ($md['daysInMonth'] * 36) }}px">
            <thead>
                <tr>
                    <th style="text-align:left;padding-left:10px;position:sticky;left:0;z-index:2;background:#f9fafb" class="dark:bg-gray-900">Nama</th>
                    @foreach ($md['dates'] as $date)
                    @php
                        $d    = \Carbon\Carbon::parse($date);
                        $dow  = $d->dayOfWeek; // 0=Sun
                        $isWe = in_array($dow, [0, 5, 6]);
                        $hIdx = $dow === 0 ? 6 : $dow - 1;
                        $hLbl = \Modules\MutabaahTahfidz\Filament\Pages\MutabaahLaporan::HARI_LABEL[$hIdx] ?? '';
                    @endphp
                    <th class="{{ $isWe ? 'weekend-col' : '' }}" style="min-width:34px">
                        {{ $d->format('d') }}<br>
                        <span style="font-weight:400;opacity:.7;font-size:9px">{{ $hLbl }}</span>
                    </th>
                    @endforeach
                    <th>Ayat</th>
                    <th>Setor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($md['rows'] as $row)
                <tr>
                    <td class="name-cell" style="position:sticky;left:0;background:#fff;z-index:1" class="dark:bg-gray-800">
                        {{ $row['siswa']->nama_lengkap }}
                    </td>
                    @foreach ($md['dates'] as $date)
                    @php
                        $rec  = $row['dayData'][$date] ?? null;
                        $isWe = in_array(\Carbon\Carbon::parse($date)->dayOfWeek, [0,5,6]);
                    @endphp
                    <td class="{{ $isWe ? 'weekend-col' : '' }}" style="padding:4px 2px">
                        @if ($rec)
                            <span class="sc sc-{{ $rec->status }}"
                                title="{{ \Modules\MutabaahTahfidz\Models\MutabaahRecord::STATUS[$rec->status] }}">
                                {{ \Modules\MutabaahTahfidz\Models\MutabaahRecord::statusEmoji($rec->status) }}
                            </span>
                            @if ($rec->jumlah_ayat > 0)
                            <div style="font-size:9px;color:#6b7280">{{ $rec->jumlah_ayat }}</div>
                            @endif
                        @else
                            <span style="color:#e5e7eb;font-size:10px">·</span>
                        @endif
                    </td>
                    @endforeach
                    <td style="font-weight:700;color:#15803d">{{ $row['totalAyat'] }}</td>
                    <td style="color:#6b7280;font-size:11px">{{ $row['setoran'] }}/{{ $md['daysInMonth'] }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align:left;padding-left:10px;position:sticky;left:0">Total</td>
                    @foreach ($md['dates'] as $date)
                    @php $dt = collect($md['rows'])->sum(fn($r) => $r['dayData'][$date]?->jumlah_ayat ?? 0); @endphp
                    <td style="font-size:10px">{{ $dt ?: '' }}</td>
                    @endforeach
                    <td>{{ $md['totalAyat'] }}</td>
                    <td>{{ $md['totalSetor'] }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="mt-card" style="text-align:center;padding:32px;color:#9ca3af">
        <p style="font-size:13px">Belum ada data setoran untuk {{ $this->getMonthLabel() }}.</p>
    </div>
    @endif
    @endif

    {{-- Legend --}}
    <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;font-size:11px;color:#6b7280">
        <span style="font-weight:600">Keterangan:</span>
        @foreach(\Modules\MutabaahTahfidz\Models\MutabaahRecord::STATUS as $k => $v)
        <span style="display:inline-flex;align-items:center;gap:3px">
            {{ \Modules\MutabaahTahfidz\Models\MutabaahRecord::statusEmoji($k) }} {{ $v }}
        </span>
        @endforeach
        <span>· A/angka = jumlah ayat</span>
    </div>

    @endif {{-- end if kelas_id --}}
</div>

@push('scripts')
<script>
// ── Copy WA Pekanan ─────────────────────────────────────────────────
function copyWAPekanan() {
    const data  = @js($this->weekData ?? []);
    if (!data.rows?.length) { alert('Tidak ada data.'); return; }

    const hariF  = @js(\Modules\MutabaahTahfidz\Filament\Pages\MutabaahLaporan::HARI_FULL);
    const wLabel = @js($this->getWeekLabel());
    const kNama  = @js(\Modules\Kelas\Models\Kelas::find($kelas_id)?->nama_kelas ?? '');

    let t  = `📖 *LAPORAN MUTABAAH TAHFIDZ*\n`;
    t     += `🏫 ${kNama}\n📅 ${wLabel}\n\n`;

    data.rows.forEach((row, i) => {
        t += `*${i+1}. ${row.siswa.nama_lengkap}*\n`;
        const statusMap={lanjut:'Lanjut',ulang:'Ulang',membaca:'Membaca',tasmi:'Tasmi',tidak_setoran:'Tdk Setoran',tidak_masuk:'Tdk Masuk'};
        const emo={lanjut:'✅',ulang:'🔁',membaca:'📖',tasmi:'🎓',tidak_setoran:'❌',tidak_masuk:'⬜'};
        data.dates.forEach((date, di) => {
            const rec = row.dayData[date];
            if (rec) {
                t += `   ${emo[rec.status]??'❓'} ${hariF[di]}: ${statusMap[rec.status]??rec.status}`;
                if (rec.jumlah_ayat > 0) t += ` (${rec.jumlah_ayat} ayat)`;
                t += '\n';
            }
        });
        t += `   📊 Total: ${row.totalAyat} ayat | Setor: ${row.setoran} hari\n\n`;
    });
    t += `📈 *Total Ayat: ${data.totalAyat} | Santri: ${data.totalSiswa}*`;
    doClipboard(t, event);
}

// ── Copy WA Bulanan ─────────────────────────────────────────────────
function copyWABulanan() {
    const data   = @js($this->monthData ?? []);
    if (!data.rows?.length) { alert('Tidak ada data.'); return; }

    const mLabel = @js($this->getMonthLabel());
    const kNama  = @js(\Modules\Kelas\Models\Kelas::find($kelas_id)?->nama_kelas ?? '');

    let t  = `📖 *LAPORAN MUTABAAH TAHFIDZ BULANAN*\n`;
    t     += `🏫 ${kNama}\n📅 ${mLabel}\n\n`;

    // Ringkasan per minggu per santri (lebih ringkas dari per-hari)
    data.rows.forEach((row, i) => {
        t += `*${i+1}. ${row.siswa.nama_lengkap}*\n`;
        if (row.perMinggu) {
            row.perMinggu.forEach(w => {
                if (w.ayat > 0 || w.setor > 0) {
                    t += `   📋 ${w.label}: ${w.ayat} ayat (${w.setor} hari)\n`;
                }
            });
        }
        t += `   ✅ Total: *${row.totalAyat} ayat* | Setoran: ${row.setoran} hari`;
        if (row.tidak > 0) t += ` | ❌ Tdk setor: ${row.tidak}`;
        t += '\n\n';
    });

    t += `📈 *RINGKASAN BULAN*\n`;
    t += `Total Ayat: ${data.totalAyat}\n`;
    t += `Total Setoran: ${data.totalSetor}\n`;
    t += `Santri Aktif: ${data.totalSiswa}`;
    doClipboard(t, event);
}

// ── Clipboard helper ────────────────────────────────────────────────
function doClipboard(text, evt) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = evt.target.closest('button');
        if (!btn) return;
        const orig = btn.innerHTML;
        btn.innerHTML = '✅ Tersalin!';
        setTimeout(() => { btn.innerHTML = orig; }, 2000);
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = text; document.body.appendChild(ta);
        ta.select(); document.execCommand('copy');
        document.body.removeChild(ta);
        alert('Teks berhasil disalin!');
    });
}
</script>
@endpush

</x-filament-panels::page>

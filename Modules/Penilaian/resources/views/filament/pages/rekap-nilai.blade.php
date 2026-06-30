<x-filament-panels::page>
<style>
.mt-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; }
.dark .mt-card { background:#1f2937; border-color:#374151; }
.mt-label { font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:#6b7280; margin-bottom:4px; }
.dark .mt-label { color:#9ca3af; }
.mt-select { width:100%; padding:8px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:13px; background:#fff; color:#111827; outline:none; }
.mt-select:focus { border-color:#16a34a; }
.dark .mt-select { background:#111827; border-color:#374151; color:#f3f4f6; }
.predikat-a { background:#dcfce7; color:#15803d; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; }
.predikat-b { background:#dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; }
.predikat-c { background:#fef9c3; color:#a16207; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; }
.predikat-d { background:#fee2e2; color:#b91c1c; padding:2px 8px; border-radius:999px; font-size:11px; font-weight:700; }
</style>

<div style="display:flex; flex-direction:column; gap:14px;">

    {{-- Filter + Actions --}}
    <div class="mt-card">
        <div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:12px;">
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:10px; flex:1;">
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

            @if($kelas_id && $tahun_ajaran_id)
            <div style="display:flex; gap:8px; flex-shrink:0;">
                <button wire:click="hitungUlangSemua" wire:loading.attr="disabled"
                    style="padding:8px 14px; background:#f0fdf4; color:#16a34a; border:1.5px solid #86efac; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;">
                    <span wire:loading.remove wire:target="hitungUlangSemua">🔄 Hitung Ulang</span>
                    <span wire:loading wire:target="hitungUlangSemua">Menghitung...</span>
                </button>
                <button wire:click="exportExcel"
                    style="padding:8px 14px; background:#16a34a; color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;">
                    📥 Export Excel
                </button>
            </div>
            @endif
        </div>
    </div>

    @if(!$kelas_id || !$tahun_ajaran_id)
        <div class="mt-card" style="text-align:center; padding:48px; color:#9ca3af;">
            <p style="font-size:14px; font-weight:500;">Pilih kelas dan tahun ajaran untuk melihat rekap nilai</p>
        </div>
    @elseif($this->siswaList->isEmpty())
        <div class="mt-card" style="text-align:center; padding:48px; color:#9ca3af;">
            <p style="font-size:14px; font-weight:500;">Tidak ada siswa di kelas ini</p>
        </div>
    @elseif($this->mapelList->isEmpty())
        <div class="mt-card" style="text-align:center; padding:48px; color:#9ca3af;">
            <p style="font-size:14px; font-weight:500;">Belum ada data penilaian untuk kelas dan semester ini</p>
        </div>
    @else

    {{-- Tabel Rekap --}}
    <div class="mt-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13px; min-width:600px;">
                <thead>
                    <tr style="background:#f0fdf4;">
                        <th style="padding:10px 12px; text-align:left; font-size:11px; font-weight:600; color:#15803d; border-bottom:2px solid #86efac; white-space:nowrap; min-width:40px;">#</th>
                        <th style="padding:10px 12px; text-align:left; font-size:11px; font-weight:600; color:#15803d; border-bottom:2px solid #86efac; white-space:nowrap; min-width:180px;">Nama Siswa</th>
                        @foreach($this->mapelList as $mapel)
                        <th style="padding:10px 8px; text-align:center; font-size:11px; font-weight:600; color:#15803d; border-bottom:2px solid #86efac; white-space:nowrap; min-width:80px;">
                            {{ $mapel->pelajaran }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->siswaList as $i => $siswa)
                    <tr style="border-bottom:1px solid #f3f4f6;" class="{{ $i % 2 === 0 ? '' : 'bg-gray-50 dark:bg-gray-800' }}">
                        <td style="padding:9px 12px; color:#9ca3af; font-size:12px;">{{ $i + 1 }}</td>
                        <td style="padding:9px 12px; font-weight:500; color:#111827;" class="dark:text-white">
                            {{ $siswa->nama_lengkap }}
                        </td>
                        @foreach($this->mapelList as $mapel)
                        @php
                            $rekap = $this->rekapData[$siswa->id][$mapel->id] ?? null;
                        @endphp
                        <td style="padding:9px 8px; text-align:center;">
                            @if($rekap && $rekap->nilai_akhir !== null)
                            <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
                                <span style="font-weight:700; color:#111827; font-size:14px;" class="dark:text-white">
                                    {{ number_format($rekap->nilai_akhir, 1) }}
                                </span>
                                @if($rekap->predikat)
                                <span class="predikat-{{ strtolower($rekap->predikat) }}">{{ $rekap->predikat }}</span>
                                @endif
                            </div>
                            @else
                            <span style="color:#d1d5db; font-size:12px;">—</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>

                {{-- Footer: rata-rata per mapel --}}
                <tfoot>
                    <tr style="background:#f9fafb; border-top:2px solid #e5e7eb;">
                        <td colspan="2" style="padding:9px 12px; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.04em;">
                            Rata-rata Kelas
                        </td>
                        @foreach($this->mapelList as $mapel)
                        <td style="padding:9px 8px; text-align:center; font-weight:700; color:#16a34a; font-size:13px;">
                            {{ $this->rataPerMapel[$mapel->id] !== null ? number_format($this->rataPerMapel[$mapel->id], 1) : '—' }}
                        </td>
                        @endforeach
                    </tr>
                </tfoot>
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
</div>
</x-filament-panels::page>
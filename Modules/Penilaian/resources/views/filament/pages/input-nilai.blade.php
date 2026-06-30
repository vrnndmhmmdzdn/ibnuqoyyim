<x-filament-panels::page>
<style>
.mt-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; }
.dark .mt-card { background:#1f2937; border-color:#374151; }
.mt-label { font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:#6b7280; margin-bottom:4px; }
.dark .mt-label { color:#9ca3af; }
.mt-select, .mt-input { width:100%; padding:8px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:13px; background:#fff; color:#111827; outline:none; transition:border-color .15s; }
.mt-select:focus, .mt-input:focus { border-color:#16a34a; }
.dark .mt-select, .dark .mt-input { background:#111827; border-color:#374151; color:#f3f4f6; }
.pill-btn { display:inline-flex; align-items:center; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:600; cursor:pointer; border:1.5px solid transparent; transition:all .15s; }
.pill-btn.active { background:#16a34a; color:#fff; border-color:#16a34a; }
.pill-btn.inactive { background:transparent; color:#6b7280; border-color:#e5e7eb; }
.pill-btn.inactive:hover { border-color:#16a34a; color:#16a34a; }
.item-row { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px; cursor:pointer; border:1.5px solid transparent; transition:all .15s; }
.item-row:hover { background:#f0fdf4; border-color:#bbf7d0; }
.item-row.active { background:#f0fdf4; border-color:#16a34a; }
.dark .item-row:hover, .dark .item-row.active { background:#052e16; border-color:#16a34a; }
.nilai-input { width:72px; padding:6px 8px; border:1.5px solid #e5e7eb; border-radius:6px; font-size:13px; text-align:center; outline:none; }
.nilai-input:focus { border-color:#16a34a; }
.nilai-input.rendah { background:#fef2f2; border-color:#fca5a5; }
.nilai-input.tinggi { background:#f0fdf4; border-color:#86efac; }
.dark .nilai-input { background:#1f2937; border-color:#374151; color:#f3f4f6; }
</style>

<div style="display:flex; flex-direction:column; gap:14px;">

    {{-- Filter Bar --}}
    <div class="mt-card">
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:10px; align-items:end;">
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
                <div class="mt-label">Mata Pelajaran</div>
                <select wire:model.live="mata_pelajaran_id" class="mt-select">
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($this->mapelList as $id => $nama)
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
    </div>

    @if(!$kelas_id || !$mata_pelajaran_id)
        <div class="mt-card" style="text-align:center; padding:48px 16px; color:#9ca3af;">
            <svg style="width:48px;height:48px;margin:0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p style="font-size:14px; font-weight:500;">Pilih kelas dan mata pelajaran untuk mulai input nilai</p>
        </div>
    @else

    <div style="display:grid; grid-template-columns:280px 1fr; gap:14px; align-items:start;">

        {{-- Kolom Kiri: Daftar Item --}}
        <div style="display:flex; flex-direction:column; gap:10px;">

            {{-- Tab Jenis --}}
            <div class="mt-card" style="padding:10px;">
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    @foreach(['harian' => 'NH', 'tugas' => 'NT', 'pts' => 'PTS', 'pas' => 'PAS'] as $jenis => $label)
                    <button wire:click="$set('jenis_tab', '{{ $jenis }}')"
                        class="pill-btn {{ $jenis_tab === $jenis ? 'active' : 'inactive' }}">
                        {{ $label }}
                        @php $count = count($this->itemList[$jenis] ?? []); @endphp
                        @if($count > 0)
                        <span style="margin-left:4px; background:rgba(255,255,255,.3); border-radius:999px; padding:0 6px; font-size:11px;">{{ $count }}</span>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Daftar item --}}
            <div class="mt-card" style="padding:10px;">
                <div class="mt-label" style="margin-bottom:8px;">
                    {{ \Modules\Penilaian\Models\PenilaianItem::JENIS[$jenis_tab] ?? $jenis_tab }}
                </div>

                @php $items = $this->itemList[$jenis_tab] ?? collect(); @endphp

                @if($items->isEmpty())
                    <p style="font-size:12px; color:#9ca3af; text-align:center; padding:16px 0;">Belum ada item</p>
                @else
                    @foreach($items as $item)
                    <div class="item-row {{ $active_item_id === $item->id ? 'active' : '' }}"
                         wire:click="pilihItem({{ $item->id }})">
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:13px; font-weight:600; color:#111827; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" class="dark:text-white">
                                {{ $item->judul }}
                            </p>
                            <p style="font-size:11px; color:#9ca3af; margin:0;">
                                {{ $item->tanggal->format('d M Y') }}
                            </p>
                        </div>
                        <button wire:click.stop="hapusItem({{ $item->id }})"
                            wire:confirm="Hapus item ini dan semua nilainya?"
                            style="color:#ef4444; opacity:.6; flex-shrink:0;"
                            title="Hapus">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                @endif

                {{-- Form tambah item --}}
                @if($show_form_item)
                <div style="margin-top:10px; padding:10px; background:#f0fdf4; border-radius:8px; border:1.5px solid #bbf7d0;">
                    <div style="margin-bottom:6px;">
                        <div class="mt-label">Judul</div>
                        <input type="text" wire:model="form_judul" class="mt-input" placeholder="Contoh: Ulangan Harian 1">
                        @error('form_judul')<p style="color:#ef4444; font-size:11px; margin-top:2px;">{{ $message }}</p>@enderror
                    </div>
                    <div style="margin-bottom:8px;">
                        <div class="mt-label">Tanggal</div>
                        <input type="date" wire:model="form_tanggal" class="mt-input">
                        @error('form_tanggal')<p style="color:#ef4444; font-size:11px; margin-top:2px;">{{ $message }}</p>@enderror
                    </div>
                    <div style="display:flex; gap:6px;">
                        <button wire:click="tambahItem"
                            style="flex:1; padding:6px; background:#16a34a; color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                            Simpan
                        </button>
                        <button wire:click="$set('show_form_item', false)"
                            style="padding:6px 10px; border:1.5px solid #e5e7eb; border-radius:6px; font-size:12px; cursor:pointer; background:#fff;">
                            Batal
                        </button>
                    </div>
                </div>
                @else
                <button wire:click="$set('show_form_item', true)"
                    style="width:100%; margin-top:8px; padding:7px; border:1.5px dashed #86efac; border-radius:8px; color:#16a34a; font-size:12px; font-weight:600; background:transparent; cursor:pointer;">
                    + Tambah Item Baru
                </button>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Form Input Nilai --}}
        @if($active_item_id && $this->activeItem)
        <div class="mt-card">

            {{-- Header item aktif --}}
            <div style="display:flex; align-items:start; justify-content:space-between; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #e5e7eb;">
                <div>
                    <p style="font-size:16px; font-weight:700; color:#111827; margin:0;" class="dark:text-white">
                        {{ $this->activeItem->judul }}
                    </p>
                    <p style="font-size:12px; color:#9ca3af; margin:2px 0 0;">
                        {{ \Modules\Penilaian\Models\PenilaianItem::JENIS[$this->activeItem->jenis] }}
                        · {{ $this->activeItem->tanggal->format('d M Y') }}
                    </p>
                </div>

                {{-- Statistik --}}
                @if(!empty($this->rekapItem) && $this->rekapItem['count'] > 0)
                <div style="display:flex; gap:12px; font-size:12px; text-align:center;">
                    <div>
                        <p style="color:#9ca3af; margin:0;">Rata-rata</p>
                        <p style="font-weight:700; color:#16a34a; margin:0; font-size:15px;">{{ $this->rekapItem['rata'] }}</p>
                    </div>
                    <div>
                        <p style="color:#9ca3af; margin:0;">Min</p>
                        <p style="font-weight:700; color:#ef4444; margin:0; font-size:15px;">{{ $this->rekapItem['min'] }}</p>
                    </div>
                    <div>
                        <p style="color:#9ca3af; margin:0;">Max</p>
                        <p style="font-weight:700; color:#3b82f6; margin:0; font-size:15px;">{{ $this->rekapItem['max'] }}</p>
                    </div>
                    <div>
                        <p style="color:#9ca3af; margin:0;">Diisi</p>
                        <p style="font-weight:700; color:#111827; margin:0; font-size:15px;" class="dark:text-white">{{ $this->rekapItem['count'] }}/{{ $this->siswaList->count() }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Tabel nilai --}}
            @if($this->siswaList->isEmpty())
                <p style="text-align:center; color:#9ca3af; padding:32px 0; font-size:13px;">
                    Tidak ada siswa di kelas ini.
                </p>
            @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="background:#f9fafb;">
                            <th style="padding:8px 10px; text-align:left; font-weight:600; color:#6b7280; font-size:11px; border-bottom:1px solid #e5e7eb;">#</th>
                            <th style="padding:8px 10px; text-align:left; font-weight:600; color:#6b7280; font-size:11px; border-bottom:1px solid #e5e7eb;">Nama Siswa</th>
                            <th style="padding:8px 10px; text-align:center; font-weight:600; color:#6b7280; font-size:11px; border-bottom:1px solid #e5e7eb; width:90px;">Nilai</th>
                            <th style="padding:8px 10px; text-align:left; font-weight:600; color:#6b7280; font-size:11px; border-bottom:1px solid #e5e7eb;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->siswaList as $i => $siswa)
                        @php
                            $nilaiVal = $nilaiInput[$siswa->id]['nilai'] ?? '';
                            $nilaiClass = '';
                            if ($nilaiVal !== '' && $nilaiVal !== null) {
                                $nilaiClass = (float)$nilaiVal < 60 ? 'rendah' : ((float)$nilaiVal >= 90 ? 'tinggi' : '');
                            }
                        @endphp
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:8px 10px; color:#9ca3af; font-size:12px;">{{ $i + 1 }}</td>
                            <td style="padding:8px 10px; font-weight:500; color:#111827;" class="dark:text-white">
                                {{ $siswa->nama_lengkap }}
                                <span style="font-size:11px; color:#9ca3af; margin-left:4px;">{{ $siswa->nis }}</span>
                            </td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" min="0" max="100" step="0.5"
                                    wire:model.lazy="nilaiInput.{{ $siswa->id }}.nilai"
                                    class="nilai-input {{ $nilaiClass }}"
                                    placeholder="–">
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text"
                                    wire:model.lazy="nilaiInput.{{ $siswa->id }}.catatan"
                                    style="width:100%; padding:5px 8px; border:1.5px solid #e5e7eb; border-radius:6px; font-size:12px; outline:none; background:transparent; color:#374151;"
                                    placeholder="Opsional...">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:14px; padding-top:12px; border-top:1px solid #e5e7eb;">
                <button wire:click="simpanSemua" wire:loading.attr="disabled"
                    style="padding:9px 24px; background:#16a34a; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px;">
                    <span wire:loading.remove wire:target="simpanSemua">💾 Simpan Semua Nilai</span>
                    <span wire:loading wire:target="simpanSemua">Menyimpan...</span>
                </button>
            </div>
            @endif
        </div>
        @else
        <div class="mt-card" style="text-align:center; padding:48px 16px; color:#9ca3af;">
            <svg style="width:40px;height:40px;margin:0 auto 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <p style="font-size:13px; font-weight:500;">Pilih item di kiri untuk mulai mengisi nilai</p>
        </div>
        @endif

    </div>
    @endif

</div>
</x-filament-panels::page>
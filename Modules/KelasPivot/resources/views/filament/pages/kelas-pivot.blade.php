<x-filament-panels::page>
<style>
.mt-card { background:#fff; border:0.5px solid #e5e7eb; border-radius:14px; padding:16px; }
.dark .mt-card { background:#1f2937; border-color:#374151; }
.mt-label { font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:#6b7280; margin-bottom:4px; }
.dark .mt-label { color:#9ca3af; }
.mt-select, .mt-input { width:100%; padding:9px 12px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:13px; background:#fff; color:#111827; outline:none; }
.mt-select:focus, .mt-input:focus { border-color:#16a34a; }
.dark .mt-select, .dark .mt-input { background:#111827; border-color:#374151; color:#f3f4f6; }
.siswa-item { display:flex; align-items:center; gap:8px; padding:8px 10px; border-bottom:0.5px solid #f3f4f6; }
.dark .siswa-item { border-color:#374151; }
.siswa-item:last-child { border-bottom:none; }
.siswa-avatar { width:28px; height:28px; border-radius:50%; background:#dcfce7; color:#15803d; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; }
.add-btn { padding:4px 12px; border-radius:99px; background:#16a34a; color:#fff; border:none; font-size:12px; font-weight:500; cursor:pointer; flex-shrink:0; }
.add-btn:hover { background:#15803d; }
.remove-btn { padding:4px 10px; border-radius:99px; background:#fee2e2; color:#dc2626; border:none; font-size:12px; font-weight:500; cursor:pointer; flex-shrink:0; }
.remove-btn:hover { background:#fecaca; }
</style>

<div style="display:flex;flex-direction:column;gap:14px">

    {{-- Controls --}}
    <div class="mt-card">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <div>
                <div class="mt-label">Kelas</div>
                <select wire:model.live="kelas_id" class="mt-select">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($this->kelasList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <div class="mt-label">Tahun Ajaran</div>
                <select wire:model.live="tahun_ajaran_id" class="mt-select">
                    @foreach ($this->tahunAjaranList as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if (!$kelas_id)
        <div class="mt-card" style="text-align:center;padding:40px;color:#9ca3af">
            <p style="font-size:14px;font-weight:500">Pilih kelas untuk mengatur anggota</p>
        </div>
    @else

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">

        {{-- Students in class --}}
        <div class="mt-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                <div class="mt-label">Anggota Kelas ({{ $this->siswaKelas->count() }})</div>
            </div>
            @if ($this->siswaKelas->isEmpty())
                <p style="color:#9ca3af;font-size:12px;text-align:center;padding:20px">
                    Belum ada siswa di kelas ini.
                </p>
            @else
                @foreach ($this->siswaKelas as $s)
                <div class="siswa-item">
                    <div class="siswa-avatar">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                    <div style="flex:1;font-size:13px;font-weight:500;color:#111827" class="dark:text-white">{{ $s->nama_lengkap }}</div>
                    <button wire:click="hapusSiswa({{ $s->id }})"
                        wire:confirm="Keluarkan {{ $s->nama_lengkap }} dari kelas ini?"
                        class="remove-btn">Keluar</button>
                </div>
                @endforeach
            @endif
        </div>

        {{-- Available students --}}
        <div class="mt-card">
            <div style="margin-bottom:10px">
                <div class="mt-label" style="margin-bottom:6px">Tambah Siswa</div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama siswa..." class="mt-input">
            </div>
            @if ($this->siswaAvailable->isEmpty())
                <p style="color:#9ca3af;font-size:12px;text-align:center;padding:20px">
                    {{ $search ? 'Siswa tidak ditemukan.' : 'Semua siswa sudah masuk kelas.' }}
                </p>
            @else
                <div style="max-height:400px;overflow-y:auto">
                    @foreach ($this->siswaAvailable as $s)
                    <div class="siswa-item">
                        <div class="siswa-avatar" style="background:#dbeafe;color:#1d4ed8">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                        <div style="flex:1;font-size:13px;color:#111827" class="dark:text-white">{{ $s->nama_lengkap }}</div>
                        <button wire:click="tambahSiswa({{ $s->id }})" class="add-btn">+ Tambah</button>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
    @endif
</div>
</x-filament-panels::page>
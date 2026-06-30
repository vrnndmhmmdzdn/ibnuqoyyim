<x-filament-panels::page>
<style>
.mt-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; }
.dark .mt-card { background:#1f2937; border-color:#374151; }
.mt-label { font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:#6b7280; margin-bottom:4px; }
.dark .mt-label { color:#9ca3af; }
.mt-select { width:100%; padding:8px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:13px; background:#fff; color:#111827; outline:none; }
.mt-select:focus { border-color:#16a34a; }
.dark .mt-select { background:#111827; border-color:#374151; color:#f3f4f6; }
.bobot-input { width:100%; padding:10px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:16px; font-weight:700; text-align:center; background:#fff; color:#111827; outline:none; transition:border-color .15s; }
.bobot-input:focus { border-color:#16a34a; }
.dark .bobot-input { background:#111827; border-color:#374151; color:#f3f4f6; }
.bobot-card { border-radius:10px; padding:16px; border:1.5px solid; text-align:center; }
</style>

<div style="display:flex; flex-direction:column; gap:14px; max-width:640px;">

    {{-- Tahun Ajaran --}}
    <div class="mt-card">
        <div class="mt-label">Tahun Ajaran</div>
        <select wire:model.live="tahun_ajaran_id" class="mt-select">
            @foreach($this->tahunAjaranList as $id => $ta)
                <option value="{{ $id }}">{{ $ta }}</option>
            @endforeach
        </select>
        <p style="font-size:12px; color:#9ca3af; margin-top:6px;">
            Konfigurasi bobot berlaku untuk semua kelas dan mapel dalam tahun ajaran yang dipilih.
        </p>
    </div>

    {{-- Bobot inputs --}}
    <div class="mt-card">
        <div style="margin-bottom:16px;">
            <p style="font-size:14px; font-weight:600; color:#111827; margin:0 0 4px;" class="dark:text-white">
                Bobot Penilaian
            </p>
            <p style="font-size:12px; color:#6b7280; margin:0;">
                Total seluruh bobot harus sama dengan <strong>100%</strong>.
            </p>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">

            {{-- NH --}}
            <div class="bobot-card" style="background:#f0fdf4; border-color:#86efac;">
                <div style="font-size:11px; font-weight:600; color:#15803d; text-transform:uppercase; letter-spacing:.04em; margin-bottom:10px;">
                    📝 Nilai Harian (NH)
                </div>
                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                    <input type="number" min="0" max="100" step="1"
                        wire:model.live="bobot_harian"
                        class="bobot-input"
                        style="border-color:#86efac; color:#15803d; max-width:90px;">
                    <span style="font-size:16px; font-weight:700; color:#15803d;">%</span>
                </div>
            </div>

            {{-- NT --}}
            <div class="bobot-card" style="background:#eff6ff; border-color:#93c5fd;">
                <div style="font-size:11px; font-weight:600; color:#1d4ed8; text-transform:uppercase; letter-spacing:.04em; margin-bottom:10px;">
                    📁 Nilai Tugas (NT)
                </div>
                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                    <input type="number" min="0" max="100" step="1"
                        wire:model.live="bobot_tugas"
                        class="bobot-input"
                        style="border-color:#93c5fd; color:#1d4ed8; max-width:90px;">
                    <span style="font-size:16px; font-weight:700; color:#1d4ed8;">%</span>
                </div>
            </div>

            {{-- PTS --}}
            <div class="bobot-card" style="background:#fffbeb; border-color:#fcd34d;">
                <div style="font-size:11px; font-weight:600; color:#b45309; text-transform:uppercase; letter-spacing:.04em; margin-bottom:10px;">
                    📄 Penilaian Tengah Semester (PTS)
                </div>
                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                    <input type="number" min="0" max="100" step="1"
                        wire:model.live="bobot_pts"
                        class="bobot-input"
                        style="border-color:#fcd34d; color:#b45309; max-width:90px;">
                    <span style="font-size:16px; font-weight:700; color:#b45309;">%</span>
                </div>
            </div>

            {{-- PAS --}}
            <div class="bobot-card" style="background:#faf5ff; border-color:#c4b5fd;">
                <div style="font-size:11px; font-weight:600; color:#6d28d9; text-transform:uppercase; letter-spacing:.04em; margin-bottom:10px;">
                    📋 Penilaian Akhir Semester (PAS)
                </div>
                <div style="display:flex; align-items:center; justify-content:center; gap:6px;">
                    <input type="number" min="0" max="100" step="1"
                        wire:model.live="bobot_pas"
                        class="bobot-input"
                        style="border-color:#c4b5fd; color:#6d28d9; max-width:90px;">
                    <span style="font-size:16px; font-weight:700; color:#6d28d9;">%</span>
                </div>
            </div>
        </div>

        {{-- Total indicator --}}
        <div style="margin-top:16px; padding:12px 16px; border-radius:10px; display:flex; align-items:center; justify-content:space-between;
            background: {{ $this->bobotValid ? '#f0fdf4' : '#fef2f2' }};
            border: 1.5px solid {{ $this->bobotValid ? '#86efac' : '#fca5a5' }};">
            <div style="font-size:13px; font-weight:600; color:{{ $this->bobotValid ? '#15803d' : '#b91c1c' }};">
                {{ $this->bobotValid ? '✅ Total bobot sudah 100%' : '⚠️ Total bobot belum 100%' }}
            </div>
            <div style="font-size:22px; font-weight:800; color:{{ $this->bobotValid ? '#16a34a' : '#ef4444' }};">
                {{ number_format($this->totalBobot, 0) }}%
            </div>
        </div>

        {{-- Visualisasi bar --}}
        @if($this->totalBobot > 0)
        <div style="margin-top:12px;">
            <div style="font-size:11px; color:#6b7280; margin-bottom:6px;">Distribusi Bobot</div>
            <div style="display:flex; height:20px; border-radius:6px; overflow:hidden; gap:1px;">
                @if($bobot_harian > 0)
                <div style="flex:{{ $bobot_harian }}; background:#16a34a; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#fff; min-width:28px;">
                    {{ round($bobot_harian) }}%
                </div>
                @endif
                @if($bobot_tugas > 0)
                <div style="flex:{{ $bobot_tugas }}; background:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#fff; min-width:28px;">
                    {{ round($bobot_tugas) }}%
                </div>
                @endif
                @if($bobot_pts > 0)
                <div style="flex:{{ $bobot_pts }}; background:#f59e0b; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#fff; min-width:28px;">
                    {{ round($bobot_pts) }}%
                </div>
                @endif
                @if($bobot_pas > 0)
                <div style="flex:{{ $bobot_pas }}; background:#8b5cf6; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:#fff; min-width:28px;">
                    {{ round($bobot_pas) }}%
                </div>
                @endif
            </div>
            <div style="display:flex; gap:12px; margin-top:6px; font-size:11px; color:#6b7280; flex-wrap:wrap;">
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#16a34a;margin-right:4px;"></span>NH</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#3b82f6;margin-right:4px;"></span>NT</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f59e0b;margin-right:4px;"></span>PTS</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#8b5cf6;margin-right:4px;"></span>PAS</span>
            </div>
        </div>
        @endif
    </div>

    {{-- Formula preview --}}
    <div class="mt-card" style="background:#f8fafc;">
        <div class="mt-label" style="margin-bottom:8px;">Formula Nilai Akhir</div>
        <code style="font-size:12px; color:#374151; background:#f1f5f9; padding:10px 14px; border-radius:8px; display:block; line-height:1.8;">
            NA = (rata_NH × <strong style="color:#16a34a;">{{ number_format($bobot_harian/100, 2) }}</strong>)
               + (rata_NT × <strong style="color:#3b82f6;">{{ number_format($bobot_tugas/100, 2) }}</strong>)
               + (PTS × <strong style="color:#f59e0b;">{{ number_format($bobot_pts/100, 2) }}</strong>)
               + (PAS × <strong style="color:#8b5cf6;">{{ number_format($bobot_pas/100, 2) }}</strong>)
        </code>
        <div style="margin-top:10px; font-size:12px; color:#6b7280; line-height:1.6;">
            Predikat: <strong style="color:#16a34a;">A</strong> ≥ 90 &nbsp;|&nbsp;
            <strong style="color:#1d4ed8;">B</strong> 75–89 &nbsp;|&nbsp;
            <strong style="color:#a16207;">C</strong> 60–74 &nbsp;|&nbsp;
            <strong style="color:#b91c1c;">D</strong> &lt; 60
        </div>
    </div>

    {{-- Action buttons --}}
    <div style="display:flex; gap:10px; align-items:center;">
        <button wire:click="simpan" wire:loading.attr="disabled"
            @if(!$this->bobotValid) disabled style="opacity:.5; cursor:not-allowed;" @endif
            style="padding:10px 24px; background:#16a34a; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; flex:1; display:flex; align-items:center; justify-content:center; gap:8px;">
            <span wire:loading.remove wire:target="simpan">💾 Simpan Konfigurasi</span>
            <span wire:loading wire:target="simpan">Menyimpan...</span>
        </button>
        <button wire:click="resetDefault"
            style="padding:10px 18px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; background:#fff; color:#374151; white-space:nowrap;">
            ↩ Reset Default
        </button>
    </div>

    <p style="font-size:12px; color:#9ca3af; text-align:center;">
        Default: NH 30% · NT 20% · PTS 20% · PAS 30%
    </p>

</div>
</x-filament-panels::page>

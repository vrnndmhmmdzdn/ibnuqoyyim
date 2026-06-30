<x-filament-panels::page>
<style>
/* ── Layout ─────────────────────────────────────────────────────── */
.mt-wrap { display: flex; flex-direction: column; gap: 14px; }

/* ── Cards ──────────────────────────────────────────────────────── */
.mt-card {
    background: var(--color-background-primary, #fff);
    border: 0.5px solid var(--color-border-secondary, #e5e7eb);
    border-radius: 14px;
    padding: 16px;
}
.dark .mt-card { background: #1f2937; border-color: #374151; }
.mt-card-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 10px;
}
.dark .mt-card-label { color: #9ca3af; }

/* ── Student Grid ───────────────────────────────────────────────── */
.siswa-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.siswa-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 12px;
    border-radius: 10px;
    border: 1.5px solid #e5e7eb;
    background: #f9fafb;
    cursor: pointer;
    transition: all .15s;
    text-align: left;
    width: 100%;
}
.dark .siswa-btn { background: #111827; border-color: #374151; }
.siswa-btn:hover { border-color: #16a34a; background: #f0fdf4; }
.dark .siswa-btn:hover { border-color: #16a34a; background: #052e16; }
.siswa-btn.done {
    border-color: #16a34a; background: #f0fdf4;
    color: #15803d;
}
.dark .siswa-btn.done { background: #052e16; color: #4ade80; }
.siswa-btn.selected {
    border-color: #16a34a; background: #16a34a; color: #fff;
}
.dark .siswa-btn.selected { background: #15803d; }
.siswa-btn.done-selected {
    border-color: #f59e0b; background: #f59e0b; color: #fff;
}
.siswa-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: #dcfce7; color: #15803d;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.siswa-btn.selected .siswa-avatar { background: rgba(255,255,255,.25); color:#fff; }
.siswa-btn.done-selected .siswa-avatar { background: rgba(255,255,255,.25); color:#fff; }
.siswa-name { font-size: 12px; font-weight: 500; line-height: 1.3; }
.siswa-sub  { font-size: 10px; opacity: .7; }

/* ── Pill Buttons ───────────────────────────────────────────────── */
.pill-group { display: flex; flex-wrap: wrap; gap: 6px; }
.pill-btn {
    padding: 6px 14px; border-radius: 99px;
    border: 1.5px solid #e5e7eb; background: #f9fafb;
    font-size: 12px; font-weight: 500; cursor: pointer;
    transition: all .15s; color: #374151;
}
.dark .pill-btn { background: #111827; border-color: #374151; color: #d1d5db; }
.pill-btn:hover { border-color: #6b7280; }

/* Status active states */
.pill-btn.p-lanjut.active         { background:#16a34a; border-color:#16a34a; color:#fff; }
.pill-btn.p-ulang.active          { background:#ea580c; border-color:#ea580c; color:#fff; }
.pill-btn.p-membaca.active        { background:#2563eb; border-color:#2563eb; color:#fff; }
.pill-btn.p-tasmi.active          { background:#7c3aed; border-color:#7c3aed; color:#fff; }
.pill-btn.p-tidak_setoran.active  { background:#dc2626; border-color:#dc2626; color:#fff; }
.pill-btn.p-tidak_masuk.active    { background:#6b7280; border-color:#6b7280; color:#fff; }

/* Nilai active states */
.pill-btn.n-rasib.active         { background:#dc2626; border-color:#dc2626; color:#fff; }
.pill-btn.n-jayyid.active        { background:#d97706; border-color:#d97706; color:#fff; }
.pill-btn.n-jayyid_jiddan.active { background:#2563eb; border-color:#2563eb; color:#fff; }
.pill-btn.n-mumtaz.active        { background:#16a34a; border-color:#16a34a; color:#fff; }

/* ── Input ──────────────────────────────────────────────────────── */
.mt-select, .mt-input, .mt-textarea {
    width: 100%; padding: 9px 12px;
    border: 1.5px solid #e5e7eb; border-radius: 10px;
    font-size: 13px; background: #fff; color: #111827;
    outline: none; transition: border-color .15s;
}
.mt-select:focus, .mt-input:focus, .mt-textarea:focus { border-color: #16a34a; }
.dark .mt-select, .dark .mt-input, .dark .mt-textarea {
    background: #111827; border-color: #374151; color: #f3f4f6;
}
.mt-input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.mt-label { font-size: 11px; color: #6b7280; margin-bottom: 4px; font-weight: 500; }
.dark .mt-label { color: #9ca3af; }
.ayat-hint { font-size: 11px; color: #6b7280; margin-top: 4px; }
.dark .ayat-hint { color: #9ca3af; }

/* ── Info box ───────────────────────────────────────────────────── */
.info-box {
    padding: 10px 12px; border-radius: 10px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    font-size: 12px; color: #15803d;
}
.dark .info-box { background: #052e16; border-color: #166534; color: #4ade80; }
.edit-box {
    padding: 10px 12px; border-radius: 10px;
    background: #fffbeb; border: 1px solid #fde68a;
    font-size: 12px; color: #92400e;
}
.dark .edit-box { background: #1c1207; border-color: #78350f; color: #fbbf24; }

/* ── Submit btn ─────────────────────────────────────────────────── */
.mt-submit {
    width: 100%; padding: 12px;
    background: #16a34a; color: #fff;
    border: none; border-radius: 12px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; transition: background .15s;
}
.mt-submit:hover  { background: #15803d; }
.mt-submit-draft  { background: transparent; color: #16a34a; border: 1.5px solid #16a34a; border-radius: 12px; }
.mt-submit-draft:hover { background: #f0fdf4; }

/* ── Empty state ────────────────────────────────────────────────── */
.mt-empty {
    text-align: center; padding: 40px 20px;
    color: #9ca3af;
}
.mt-empty svg { width: 48px; height: 48px; margin: 0 auto 12px; opacity: .4; }

/* ── Record badge ───────────────────────────────────────────────── */
.rec-badge {
    display: inline-flex; align-items: center;
    padding: 2px 8px; border-radius: 99px;
    font-size: 11px; font-weight: 500;
}
</style>

<div class="mt-wrap">

    {{-- ── Header: Kelas + Tanggal ──────────────────────────────── --}}
    <div class="mt-card">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
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
                <div class="mt-label">Tanggal</div>
                <input type="date" wire:model.live="tanggal"
                    max="{{ today()->format('Y-m-d') }}"
                    class="mt-input">
            </div>
        </div>
    </div>

    @if (!$kelas_id)
        {{-- Empty state --}}
        <div class="mt-card mt-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p style="font-weight:600;font-size:14px;color:#374151" class="dark:text-white">Pilih kelas terlebih dahulu</p>
            <p style="font-size:12px;margin-top:4px">Gunakan dropdown di atas untuk memilih kelas yang akan diinput setorannya.</p>
        </div>

    @else

        {{-- ── Student Grid ──────────────────────────────────────── --}}
        <div class="mt-card">
            <div class="mt-card-label">
                Pilih Santri
                <span style="font-weight:400;text-transform:none;letter-spacing:0;margin-left:4px">
                    ({{ $this->recordsHariIni->count() }}/{{ $this->siswaList->count() }} sudah input)
                </span>
            </div>

            @if ($this->siswaList->isEmpty())
                <div class="mt-empty" style="padding:20px">
                    <p style="font-size:13px">Belum ada siswa di kelas ini.
                        <a href="{{ route('filament.admin.pages.mutabaah-kelas-setup') }}"
                            class="text-green-600 hover:underline">Setup kelas →</a>
                    </p>
                </div>
            @else
                <div class="siswa-grid">
                    @foreach ($this->siswaList as $siswa)
                        @php
                            $rec          = $this->recordsHariIni[$siswa->id] ?? null;
                            $isSelected   = $selected_siswa_id === $siswa->id;
                            $isDone       = $rec !== null;
                            $initial      = strtoupper(substr($siswa->nama_lengkap, 0, 1));
                            $btnClass     = $isDone && $isSelected ? 'done-selected'
                                          : ($isSelected ? 'selected'
                                          : ($isDone ? 'done' : ''));
                        @endphp
                        <button wire:click="pilihSiswa({{ $siswa->id }})" class="siswa-btn {{ $btnClass }}">
                            <div class="siswa-avatar">{{ $initial }}</div>
                            <div style="flex:1;min-width:0">
                                <div class="siswa-name">{{ $siswa->nama_lengkap }}</div>
                                @if ($rec)
                                    <div class="siswa-sub">
                                        {{ \Modules\MutabaahTahfidz\Models\MutabaahRecord::STATUS[$rec->status] }}
                                        @if ($rec->surah)
                                            · {{ $rec->surah->nama_surah }}
                                            {{ $rec->ayat_awal }}–{{ $rec->ayat_akhir }}
                                        @endif
                                    </div>
                                @else
                                    <div class="siswa-sub">Belum input</div>
                                @endif
                            </div>
                            @if ($isDone && !$isSelected)
                                <svg style="width:14px;height:14px;flex-shrink:0;color:#16a34a" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Form Section ──────────────────────────────────────── --}}
        @if ($selected_siswa_id)
        <div class="mt-card">

            {{-- Header student --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="siswa-avatar" style="width:36px;height:36px;font-size:14px;background:#dcfce7;color:#15803d">
                        {{ strtoupper(substr($this->selectedSiswa?->nama_lengkap ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:14px;color:#111827" class="dark:text-white">
                            {{ $this->selectedSiswa?->nama_lengkap }}
                        </div>
                        <div style="font-size:11px;color:#6b7280">
                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                </div>
                @if ($edit_id)
                    <span class="rec-badge" style="background:#fef3c7;color:#92400e">✏️ Mode Edit</span>
                @endif
            </div>

            {{-- Edit info --}}
            @if ($edit_id)
                <div class="edit-box" style="margin-bottom:12px">
                    ✏️ Kamu sedang mengedit setoran yang sudah ada. Simpan untuk memperbarui.
                </div>
            @elseif ($this->lastRecord)
                <div class="info-box" style="margin-bottom:12px">
                    📌 Terakhir: <strong>{{ $this->lastRecord->surah?->nama_surah }}</strong>
                    ayat {{ $this->lastRecord->ayat_awal }}–{{ $this->lastRecord->ayat_akhir }}
                    ({{ \Carbon\Carbon::parse($this->lastRecord->tanggal)->locale('id')->translatedFormat('d M Y') }})
                </div>
            @endif

            {{-- Status pills --}}
            <div class="mt-label">Status Setoran</div>
            <div class="pill-group" style="margin-bottom:14px">
                @foreach (\Modules\MutabaahTahfidz\Models\MutabaahRecord::STATUS as $val => $label)
                    <button wire:click="setStatus('{{ $val }}')"
                        class="pill-btn p-{{ $val }} {{ $status === $val ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Surah + Ayat section --}}
            @if ($this->showSurahSection)
                <div style="margin-bottom:14px">
                    <div class="mt-label">Surah</div>
                    <select wire:model.live="surah_id" class="mt-select" style="margin-bottom:8px">
                        <option value="">-- Pilih Surah --</option>
                        @foreach ($this->surahList as $s)
                            <option value="{{ $s->id }}">{{ $s->no_surah }}. {{ $s->nama_surah }} ({{ $s->jumlah_ayat }} ayat, Juz {{ $s->juz }})</option>
                        @endforeach
                    </select>

                    <div class="mt-input-row">
                        <div>
                            <div class="mt-label">Ayat Awal</div>
                            <input type="number" wire:model.live="ayat_awal"
                                min="1" max="{{ $this->selectedSurah?->jumlah_ayat ?? 999 }}"
                                class="mt-input">
                        </div>
                        <div>
                            <div class="mt-label">Ayat Akhir</div>
                            <input type="number" wire:model.live="ayat_akhir"
                                min="{{ $ayat_awal }}" max="{{ $this->selectedSurah?->jumlah_ayat ?? 999 }}"
                                class="mt-input">
                        </div>
                    </div>

                    <div class="ayat-hint">
                        Jumlah: <strong>{{ $this->jumlahAyat }} ayat</strong>
                        @if ($this->selectedSurah)
                            · Total surah: {{ $this->selectedSurah->jumlah_ayat }} ayat
                        @endif
                    </div>
                </div>
            @endif

            {{-- Nilai pills --}}
            @if ($this->showNilaiSection)
                <div style="margin-bottom:14px">
                    <div class="mt-label">Nilai</div>
                    <div class="pill-group">
                        @foreach (\Modules\MutabaahTahfidz\Models\MutabaahRecord::NILAI as $val => $label)
                            <button wire:click="setNilai('{{ $val }}')"
                                class="pill-btn n-{{ $val }} {{ $nilai === $val ? 'active' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Catatan --}}
            <div style="margin-bottom:16px">
                <div class="mt-label">Catatan <span style="opacity:.6">(opsional)</span></div>
                <textarea wire:model="catatan" rows="2" placeholder="Catatan tambahan..."
                    class="mt-textarea"></textarea>
            </div>

            {{-- Buttons --}}
            <div style="display:flex;gap:8px">
                <button wire:click="simpan" wire:loading.attr="disabled" class="mt-submit" style="flex:1">
                    <span wire:loading.remove wire:target="simpan">
                        {{ $edit_id ? '💾 Perbarui Setoran' : '✅ Simpan Setoran' }}
                    </span>
                    <span wire:loading wire:target="simpan">Menyimpan...</span>
                </button>
                @if ($edit_id)
                    <button wire:click="hapusRecord({{ $edit_id }})"
                        wire:confirm="Hapus catatan setoran ini?"
                        style="padding:12px 16px;background:#fee2e2;color:#dc2626;border:none;border-radius:12px;cursor:pointer;font-size:13px;font-weight:500">
                        🗑️
                    </button>
                @endif
            </div>

        </div>
        @endif

    @endif
</div>
</x-filament-panels::page>
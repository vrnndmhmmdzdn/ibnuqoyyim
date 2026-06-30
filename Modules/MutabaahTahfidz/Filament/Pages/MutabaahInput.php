<?php

namespace Modules\MutabaahTahfidz\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Computed;
use Modules\Kelas\Models\Kelas;
use Modules\MutabaahTahfidz\Models\MutabaahRecord;
use Modules\MutabaahTahfidz\Models\MutabaahSurah;
use Modules\Siswa\Models\Siswa;
use UnitEnum;

class MutabaahInput extends Page
{
    protected string $view = 'mutabaah-tahfidz::filament.pages.mutabaah-input';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;
    protected static ?string $navigationLabel  = 'Input Setoran';
    protected static string|UnitEnum|null $navigationGroup = 'Mutabaah Tahfidz';
    protected static ?int $navigationSort      = 2;

    // ── State ────────────────────────────────────────────────────────

    public ?int   $kelas_id           = null;
    public string $tanggal            = '';
    public ?int   $selected_siswa_id  = null;
    public string $status             = 'lanjut';
    public ?int   $surah_id           = null;
    public int    $ayat_awal          = 1;
    public int    $ayat_akhir         = 5;
    public string $nilai              = 'jayyid';
    public string $catatan            = '';
    public ?int   $edit_id            = null;

    public function mount(): void
    {
        $this->tanggal = today()->format('Y-m-d');

        // Auto-select jika guru hanya mengajar 1 kelas
        $guruId = auth()->user()->guru?->id;
        if ($guruId) {
            $kelasIds = MutabaahRecord::where('guru_id', $guruId)
                ->distinct('kelas_id')
                ->pluck('kelas_id');
            if ($kelasIds->count() === 1) {
                $this->kelas_id = $kelasIds->first();
            }
        }
    }

    // ── Computed ─────────────────────────────────────────────────────

    #[Computed]
    public function kelasList(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }

    #[Computed]
    public function siswaList(): \Illuminate\Support\Collection
    {
        if (!$this->kelas_id) return collect();

        return Siswa::whereHas('kelas', fn ($q) =>
            $q->where('kelas.id', $this->kelas_id)
              ->where('kelas_pivot.is_aktif', true)
              ->whereNull('kelas_pivot.deleted_at')
        )->orderBy('nama_lengkap')->get();
    }

    #[Computed]
    public function recordsHariIni(): \Illuminate\Support\Collection
    {
        if (!$this->kelas_id || !$this->tanggal) return collect();

        return MutabaahRecord::with(['surah'])
            ->whereIn('siswa_id', $this->siswaList->pluck('id'))
            ->whereDate('tanggal', $this->tanggal)
            ->get()
            ->keyBy('siswa_id');
    }

    #[Computed]
    public function surahList(): \Illuminate\Support\Collection
    {
        return MutabaahSurah::orderBy('no_surah')->get();
    }

    #[Computed]
    public function selectedSurah(): ?MutabaahSurah
    {
        if (!$this->surah_id) return null;
        return MutabaahSurah::find($this->surah_id);
    }

    #[Computed]
    public function selectedSiswa(): ?Siswa
    {
        if (!$this->selected_siswa_id) return null;
        return Siswa::find($this->selected_siswa_id);
    }

    #[Computed]
    public function jumlahAyat(): int
    {
        if ($this->ayat_akhir >= $this->ayat_awal) {
            return $this->ayat_akhir - $this->ayat_awal + 1;
        }
        return 0;
    }

    #[Computed]
    public function showSurahSection(): bool
    {
        return in_array($this->status, MutabaahRecord::STATUS_NEEDS_SURAH);
    }

    #[Computed]
    public function showNilaiSection(): bool
    {
        return in_array($this->status, MutabaahRecord::STATUS_NEEDS_NILAI);
    }

    #[Computed]
    public function lastRecord(): ?MutabaahRecord
    {
        if (!$this->selected_siswa_id || !$this->kelas_id) return null;

        return MutabaahRecord::with('surah')
            ->where('siswa_id', $this->selected_siswa_id)
            ->whereDate('tanggal', '!=', $this->tanggal)
            ->whereIn('status', MutabaahRecord::STATUS_NEEDS_SURAH)
            ->latest('tanggal')
            ->first();
    }

    // ── Lifecycle hooks ──────────────────────────────────────────────

    public function updatedKelasId(): void
    {
        $this->resetFormFull();
    }

    public function updatedTanggal(): void
    {
        $this->reset(['selected_siswa_id', 'edit_id']);
        $this->resetFormFields();
        unset($this->recordsHariIni);
    }

    // ── Actions ──────────────────────────────────────────────────────

    public function pilihSiswa(int $siswaId): void
    {
        // Deselect if clicking same student
        if ($this->selected_siswa_id === $siswaId && !$this->edit_id) {
            $this->resetFormFull();
            return;
        }

        $this->selected_siswa_id = $siswaId;
        unset($this->selectedSiswa, $this->lastRecord);

        // Check if record already exists for today (edit mode)
        $existing = MutabaahRecord::where([
            'kelas_id' => $this->kelas_id,
            'siswa_id' => $siswaId,
        ])->whereDate('tanggal', $this->tanggal)->first();

        if ($existing) {
            $this->edit_id   = $existing->id;
            $this->status    = $existing->status;
            $this->surah_id  = $existing->surah_id;
            $this->ayat_awal = $existing->ayat_awal ?? 1;
            $this->ayat_akhir = $existing->ayat_akhir ?? 1;
            $this->nilai     = $existing->nilai ?? 'jayyid';
            $this->catatan   = $existing->catatan ?? '';
            return;
        }

        $this->edit_id = null;
        $this->catatan = '';

        // Auto-fill from previous record
        $lastRec = MutabaahRecord::where('siswa_id', $siswaId)
            ->whereIn('status', MutabaahRecord::STATUS_NEEDS_SURAH)
            ->latest('tanggal')
            ->first();

        if (!$lastRec) {
            $firstSurah = MutabaahSurah::orderBy('no_surah')->first();
            $this->status    = 'lanjut';
            $this->surah_id  = $firstSurah?->id;
            $this->ayat_awal = 1;
            $this->ayat_akhir = min(5, $firstSurah?->jumlah_ayat ?? 5);
            $this->nilai     = 'jayyid';
            return;
        }

        $this->surah_id = $lastRec->surah_id;
        $this->nilai    = 'jayyid';

        if ($lastRec->status === 'ulang') {
            // Repeat same range
            $this->status     = 'ulang';
            $this->ayat_awal  = $lastRec->ayat_awal ?? 1;
            $this->ayat_akhir = $lastRec->ayat_akhir ?? 1;
        } elseif ($lastRec->status === 'lanjut') {
            $this->status = 'lanjut';
            $surah        = MutabaahSurah::find($lastRec->surah_id);

            if ($surah && ($lastRec->ayat_akhir ?? 0) >= $surah->jumlah_ayat) {
                // Advance to next surah
                $nextSurah = MutabaahSurah::where('no_surah', '>', $surah->no_surah)
                    ->orderBy('no_surah')
                    ->first();

                if ($nextSurah) {
                    $this->surah_id   = $nextSurah->id;
                    $this->ayat_awal  = 1;
                    $this->ayat_akhir = min(5, $nextSurah->jumlah_ayat);
                }
            } else {
                $nextStart        = ($lastRec->ayat_akhir ?? 0) + 1;
                $this->ayat_awal  = $nextStart;
                $this->ayat_akhir = min($nextStart + 4, $surah?->jumlah_ayat ?? $nextStart);
            }
        } else {
            $this->status     = 'lanjut';
            $this->ayat_awal  = $lastRec->ayat_awal ?? 1;
            $this->ayat_akhir = $lastRec->ayat_akhir ?? 1;
        }

        unset($this->selectedSurah, $this->jumlahAyat, $this->showSurahSection, $this->showNilaiSection);
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        unset($this->showSurahSection, $this->showNilaiSection);
    }

    public function setNilai(string $nilai): void
    {
        $this->nilai = $nilai;
    }

    public function simpan(): void
    {
        $rules = [
            'kelas_id'          => 'required|exists:kelas,id',
            'selected_siswa_id' => 'required|exists:siswas,id',
            'tanggal'           => 'required|date',
            'status'            => 'required|in:' . implode(',', array_keys(MutabaahRecord::STATUS)),
        ];

        if ($this->showSurahSection) {
            $maxAyat = $this->selectedSurah?->jumlah_ayat ?? 999;
            $rules['surah_id']  = 'required|exists:mutabaah_surahs,id';
            $rules['ayat_awal'] = "required|integer|min:1|max:{$maxAyat}";
            $rules['ayat_akhir'] = "required|integer|min:{$this->ayat_awal}|max:{$maxAyat}";
        }

        $this->validate($rules);

        $needsSurah = $this->showSurahSection;
        $needsNilai = $this->showNilaiSection;

        $data = [
            'kelas_id'    => $this->kelas_id,
            'siswa_id'    => $this->selected_siswa_id,
            'tanggal'     => $this->tanggal,
            'status'      => $this->status,
            'surah_id'    => $needsSurah ? $this->surah_id : null,
            'ayat_awal'   => $needsSurah ? $this->ayat_awal : null,
            'ayat_akhir'  => $needsSurah ? $this->ayat_akhir : null,
            'jumlah_ayat' => $needsSurah ? $this->jumlahAyat : 0,
            'nilai'       => $needsNilai ? $this->nilai : null,
            'catatan'     => $this->catatan ?: null,
            'guru_id'     => auth()->user()->guru?->id,
        ];

        if ($this->edit_id) {
            MutabaahRecord::find($this->edit_id)?->update($data);
            Notification::make()->title('✅ Setoran diperbarui!')->success()->send();
        } else {
            MutabaahRecord::create($data);
            Notification::make()->title('✅ Setoran disimpan!')->success()->send();
        }

        unset($this->recordsHariIni, $this->siswaList);
        $this->resetFormFull();
    }

    public function hapusRecord(int $id): void
    {
        MutabaahRecord::find($id)?->delete();

        if ($this->edit_id === $id) {
            $this->resetFormFull();
        }

        unset($this->recordsHariIni);
        Notification::make()->title('Catatan dihapus')->success()->send();
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function resetFormFull(): void
    {
        $this->selected_siswa_id = null;
        $this->edit_id           = null;
        $this->resetFormFields();
        unset($this->selectedSiswa, $this->lastRecord, $this->selectedSurah,
              $this->jumlahAyat, $this->showSurahSection, $this->showNilaiSection);
    }

    private function resetFormFields(): void
    {
        $this->status    = 'lanjut';
        $this->surah_id  = null;
        $this->ayat_awal = 1;
        $this->ayat_akhir = 5;
        $this->nilai     = 'jayyid';
        $this->catatan   = '';
    }
}

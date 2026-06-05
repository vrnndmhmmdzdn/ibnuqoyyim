<?php

namespace Modules\KelasPivot\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Modules\Kelas\Models\Kelas;
use Modules\Siswa\Models\Siswa;
use Modules\TahunAjaran\Models\TahunAjaran;
use UnitEnum;

class KelasPivot extends Page
{
    protected string $view = 'kelas-pivot::filament.pages.kelas-pivot';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::SquaresPlus;
    protected static ?string $navigationLabel  = 'Setup Kelas';
    protected static string|UnitEnum|null $navigationGroup = 'Kelas & Siswa';
    protected static ?int $navigationSort      = 5;

    public ?int    $kelas_id        = null;
    public ?int    $tahun_ajaran_id = null;
    public array   $selected_siswa  = [];   // siswa_id[] to attach
    public string  $search          = '';

    public function mount(): void
    {
        $this->tahun_ajaran_id = TahunAjaran::where('is_aktif', true)->first()?->id;
    }

    #[Computed]
    public function kelasList(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }

    #[Computed]
    public function tahunAjaranList(): array
    {
        return TahunAjaran::orderByDesc('tahun_ajaran')->pluck('tahun_ajaran', 'id')->toArray();
    }

    /** Students already attached to the selected class this year */
    #[Computed]
    public function siswaKelas(): \Illuminate\Support\Collection
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) return collect();

        return Siswa::whereHas('kelas', fn ($q) =>
            $q->where('kelas.id', $this->kelas_id)
              ->where('kelas_pivot.tahun_ajaran_id', $this->tahun_ajaran_id)
        )->orderBy('nama_lengkap')->get();
    }

    /** Students NOT yet attached to any class this year (available to add) */
    #[Computed]
    public function siswaAvailable(): \Illuminate\Support\Collection
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) return collect();

        $query = Siswa::whereDoesntHave('kelas', fn ($q) =>
            $q->where('kelas_pivot.tahun_ajaran_id', $this->tahun_ajaran_id)
        )->where('status_siswa', 'aktif');

        if ($this->search) {
            $query->where('nama_lengkap', 'like', "%{$this->search}%");
        }

        return $query->orderBy('nama_lengkap')->get();
    }

    public function updatedKelasId(): void
    {
        $this->selected_siswa = [];
        unset($this->siswaKelas, $this->siswaAvailable);
    }

    public function updatedTahunAjaranId(): void
    {
        $this->selected_siswa = [];
        unset($this->siswaKelas, $this->siswaAvailable);
    }

    public function updatedSearch(): void
    {
        unset($this->siswaAvailable);
    }

    public function tambahSiswa(int $siswaId): void
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) return;

        $exists = DB::table('kelas_pivot')->where([
            'kelas_id'        => $this->kelas_id,
            'siswa_id'        => $siswaId,
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
        ])->exists();

        if (!$exists) {
            DB::table('kelas_pivot')->insert([
                'kelas_id'        => $this->kelas_id,
                'siswa_id'        => $siswaId,
                'tahun_ajaran_id' => $this->tahun_ajaran_id,
                'is_aktif'        => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        unset($this->siswaKelas, $this->siswaAvailable);
        Notification::make()->title('Siswa ditambahkan ke kelas')->success()->send();
    }

    public function hapusSiswa(int $siswaId): void
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) return;

        DB::table('kelas_pivot')->where([
            'kelas_id'        => $this->kelas_id,
            'siswa_id'        => $siswaId,
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
        ])->delete();

        unset($this->siswaKelas, $this->siswaAvailable);
        Notification::make()->title('Siswa dikeluarkan dari kelas')->warning()->send();
    }
}



<?php

namespace Modules\Penilaian\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Modules\Penilaian\Models\PenilaianKonfigurasi;
use Modules\TahunAjaran\Models\TahunAjaran;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class KonfigurasiPenilaian extends Page
{
    protected string $view = 'penilaian::filament.pages.konfigurasi-penilaian';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?string $navigationLabel = 'Konfigurasi';
    protected static string|UnitEnum|null $navigationGroup = 'Penilaian';
    protected static ?int $navigationSort = 4;

    public ?int   $tahun_ajaran_id = null;
    public float  $bobot_harian    = 30;
    public float  $bobot_tugas     = 20;
    public float  $bobot_pts       = 20;
    public float  $bobot_pas       = 30;

    public function mount(): void
    {
        $this->tahun_ajaran_id = TahunAjaran::where('is_aktif', true)->first()?->id;
        if ($this->tahun_ajaran_id) {
            $this->loadKonfigurasi();
        }
    }

    #[Computed]
    public function tahunAjaranList(): array
    {
        return TahunAjaran::orderByDesc('tahun_ajaran')->pluck('tahun_ajaran', 'id')->toArray();
    }

    #[Computed]
    public function totalBobot(): float
    {
        return $this->bobot_harian + $this->bobot_tugas + $this->bobot_pts + $this->bobot_pas;
    }

    #[Computed]
    public function bobotValid(): bool
    {
        return abs($this->totalBobot - 100) < 0.01;
    }

    public function updatedTahunAjaranId(): void
    {
        $this->loadKonfigurasi();
    }

    private function loadKonfigurasi(): void
    {
        if (!$this->tahun_ajaran_id) return;

        $config = PenilaianKonfigurasi::getOrDefault($this->tahun_ajaran_id);
        $this->bobot_harian = $config->bobot_harian;
        $this->bobot_tugas  = $config->bobot_tugas;
        $this->bobot_pts    = $config->bobot_pts;
        $this->bobot_pas    = $config->bobot_pas;

        unset($this->totalBobot, $this->bobotValid);
    }

    public function simpan(): void
    {
        $this->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'bobot_harian'    => 'required|numeric|min:0|max:100',
            'bobot_tugas'     => 'required|numeric|min:0|max:100',
            'bobot_pts'       => 'required|numeric|min:0|max:100',
            'bobot_pas'       => 'required|numeric|min:0|max:100',
        ]);

        if (!$this->bobotValid) {
            Notification::make()
                ->title('Total bobot harus 100%')
                ->body("Total saat ini: {$this->totalBobot}%")
                ->warning()
                ->send();
            return;
        }

        PenilaianKonfigurasi::updateOrCreate(
            ['tahun_ajaran_id' => $this->tahun_ajaran_id],
            [
                'bobot_harian' => $this->bobot_harian,
                'bobot_tugas'  => $this->bobot_tugas,
                'bobot_pts'    => $this->bobot_pts,
                'bobot_pas'    => $this->bobot_pas,
            ]
        );

        Notification::make()->title('Konfigurasi bobot berhasil disimpan')->success()->send();
    }

    public function resetDefault(): void
    {
        $this->bobot_harian = 30;
        $this->bobot_tugas  = 20;
        $this->bobot_pts    = 20;
        $this->bobot_pas    = 30;
        unset($this->totalBobot, $this->bobotValid);
    }
    public static function canAccess(): bool
    {
        return auth()->user()->role == 'admin';
    }
}
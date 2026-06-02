<?php

namespace Modules\AbsensiStaf\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Modules\AbsensiStaf\Models\AbsensiStaf;
use Modules\AbsensiStaf\Models\HariLibur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ClockInOut extends Page
{
    protected string $view = 'absensi-staf::filament.pages.clock-in-out';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFingerPrint;
    protected static ?string $navigationLabel = 'Absensi Saya';
    protected static string|UnitEnum|null $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 1;

    // Data dari JS
    public ?float $lat        = null;
    public ?float $lng        = null;
    public ?string $foto_base64 = null;
    public bool $kamera_aktif   = false;

    #[Computed]
    public function guru()
    {
        return auth()->user()->guru;
    }

    #[Computed]
    public function absensiHariIni(): ?AbsensiStaf
    {
        if (!$this->guru) return null;

        return AbsensiStaf::where('guru_id', $this->guru->id)
            ->whereDate('tanggal', today())
            ->first();
    }

    #[Computed]
    public function statusHariIni(): string
    {
        $absensi = $this->absensiHariIni;

        if (!$absensi) return 'belum_clock_in';
        if ($absensi->sudah_clock_in && !$absensi->sudah_clock_out) return 'sudah_clock_in';
        if ($absensi->sudah_clock_out) return 'sudah_clock_out';

        return 'belum_clock_in';
    }

    #[Computed]
    public function isHariLibur(): bool
    {
        $today = Carbon::today();

        // Minggu
        if ($today->dayOfWeek === 0) return true;

        // Hari libur nasional
        return HariLibur::isLibur($today);
    }

    #[Computed]
    public function jamPulang(): string
    {
        return Carbon::today()->dayOfWeek === 6
            ? AbsensiStaf::JAM_PULANG_SABTU
            : AbsensiStaf::JAM_PULANG;
    }

    public function clockIn(): void
    {
        if (!$this->guru) {
            Notification::make()
                ->title('Data guru tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        if (!$this->foto_base64) {
            Notification::make()
                ->title('Foto belum diambil')
                ->body('Silakan ambil foto selfie terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        if (!$this->lat || !$this->lng) {
            Notification::make()
                ->title('Lokasi belum terdeteksi')
                ->body('Izinkan akses lokasi di browser kamu.')
                ->warning()
                ->send();
            return;
        }

        // Simpan foto dari base64
        $fotoPath = $this->simpanFoto($this->foto_base64, 'clock-in');

        $now    = Carbon::now();
        $status = AbsensiStaf::tentukanStatus($now, Carbon::today());

        AbsensiStaf::create([
            'guru_id'      => $this->guru->id,
            'tanggal'      => today()->format('Y-m-d'),
            'clock_in_at'  => $now,
            'clock_in_foto'=> $fotoPath,
            'clock_in_lat' => $this->lat,
            'clock_in_lng' => $this->lng,
            'status'       => $status,
        ]);

        $this->reset(['foto_base64', 'lat', 'lng', 'kamera_aktif']);

        Notification::make()
            ->title('Clock In berhasil!')
            ->body('Jam masuk: ' . $now->format('H:i') . ' — Status: ' . AbsensiStaf::STATUS[$status])
            ->success()
            ->send();
    }

    public function clockOut(): void
    {
        $absensi = $this->absensiHariIni;

        if (!$absensi || !$absensi->sudah_clock_in) {
            Notification::make()
                ->title('Belum Clock In')
                ->warning()
                ->send();
            return;
        }

        if (!$this->foto_base64) {
            Notification::make()
                ->title('Foto belum diambil')
                ->body('Silakan ambil foto selfie terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        if (!$this->lat || !$this->lng) {
            Notification::make()
                ->title('Lokasi belum terdeteksi')
                ->warning()
                ->send();
            return;
        }

        $fotoPath = $this->simpanFoto($this->foto_base64, 'clock-out');
        $now      = Carbon::now();

        $absensi->update([
            'clock_out_at'  => $now,
            'clock_out_foto'=> $fotoPath,
            'clock_out_lat' => $this->lat,
            'clock_out_lng' => $this->lng,
        ]);

        $this->reset(['foto_base64', 'lat', 'lng', 'kamera_aktif']);

        Notification::make()
            ->title('Clock Out berhasil!')
            ->body('Jam pulang: ' . $now->format('H:i'))
            ->success()
            ->send();
    }

    private function simpanFoto(string $base64, string $prefix): string
    {
        $data      = explode(',', $base64);
        $imageData = base64_decode($data[1]);
        $namaFile  = $prefix . '_' . $this->guru->id . '_' . now()->format('Ymd_His') . '.jpg';
        $path      = 'absensi-foto/' . $namaFile;

        Storage::disk('local')->put($path, $imageData);

        return $path;
    }
}
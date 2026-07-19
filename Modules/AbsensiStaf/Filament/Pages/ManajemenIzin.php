<?php

namespace Modules\AbsensiStaf\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Modules\AbsensiStaf\Models\IzinStaf;
use Modules\AbsensiStaf\Models\AbsensiStaf;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManajemenIzin extends Page
{
    protected string $view = 'absensi-staf::filament.pages.manajemen-izin';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?string $navigationLabel = 'Manajemen Izin';
    protected static string|UnitEnum|null $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 5;

    // Filter
    public string $filter_status = 'menunggu';
    public string $filter_jenis  = '';
    public string $filter_bulan  = '';
    public string $filter_tahun  = '';

    // Modal approve/tolak
    public ?int $izin_id_proses  = null;
    public string $aksi_proses   = '';
    public string $catatan_admin = '';

    public function mount(): void
    {
        $this->filter_bulan = now()->format('m');
        $this->filter_tahun = now()->format('Y');
    }

    #[Computed]
    public function daftarIzin(): \Illuminate\Support\Collection
    {
        $query = IzinStaf::with(['guru', 'diprosesoleh'])
            ->orderByRaw("CASE status WHEN 'menunggu' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        if ($this->filter_status) {
            $query->where('status', $this->filter_status);
        }

        if ($this->filter_jenis) {
            $query->where('jenis', $this->filter_jenis);
        }

        if ($this->filter_bulan && $this->filter_tahun) {
            $query->whereMonth('tanggal_mulai', $this->filter_bulan)
                  ->whereYear('tanggal_mulai', $this->filter_tahun);
        }

        return $query->get();
    }

    #[Computed]
    public function totalMenunggu(): int
    {
        return IzinStaf::where('status', 'menunggu')->count();
    }

    #[Computed]
    public function bulanList(): array
    {
        return [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];
    }

    #[Computed]
    public function tahunList(): array
    {
        $tahun = now()->year;
        $list  = [];
        for ($y = $tahun; $y >= $tahun - 2; $y--) {
            $list[$y] = (string) $y;
        }
        return $list;
    }

    public function bukaModal(int $izinId, string $aksi): void
    {
        $this->izin_id_proses  = $izinId;
        $this->aksi_proses     = $aksi;
        $this->catatan_admin   = '';
    }

    public function tutupModal(): void
    {
        $this->izin_id_proses  = null;
        $this->aksi_proses     = '';
        $this->catatan_admin   = '';
    }

    public function prosesIzin(): void
    {
        $this->validate([
            'catatan_admin' => $this->aksi_proses === 'ditolak'
                ? 'required|string|min:5'
                : 'nullable|string',
        ], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
            'catatan_admin.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $izin = IzinStaf::with('guru')->find($this->izin_id_proses);

        if (!$izin || $izin->status !== 'menunggu') {
            Notification::make()
                ->title('Izin sudah diproses')
                ->warning()
                ->send();
            $this->tutupModal();
            return;
        }

        $izin->update([
            'status'        => $this->aksi_proses,
            'diproses_oleh' => auth()->id(),
            'diproses_at'   => now(),
            'catatan_admin' => $this->catatan_admin ?: null,
        ]);

        // Kalau disetujui — update atau buat record absensi per tanggal
        if ($this->aksi_proses === 'disetujui') {
            $this->buatAbsensiIzin($izin);
        }

        $label = $this->aksi_proses === 'disetujui' ? 'disetujui' : 'ditolak';

        Notification::make()
            ->title("Izin {$izin->guru?->name} berhasil {$label}")
            ->success()
            ->send();

        $this->tutupModal();
    }

    private function buatAbsensiIzin(IzinStaf $izin): void
    {
        $tanggal = Carbon::parse($izin->tanggal_mulai);
        $selesai = Carbon::parse($izin->tanggal_selesai);

        while ($tanggal->lte($selesai)) {
            // Skip Minggu
            if ($tanggal->dayOfWeek !== 0) {
                AbsensiStaf::updateOrCreate(
                    [
                        'guru_id' => $izin->guru_id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                    ],
                    [
                        'status'     => $izin->jenis, // 'izin' atau 'sakit'
                        'keterangan' => $izin->keterangan,
                    ]
                );
            }
            $tanggal->addDay();
        }
    }
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }
}
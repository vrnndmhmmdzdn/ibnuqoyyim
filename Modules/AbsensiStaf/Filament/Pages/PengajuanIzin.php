<?php

namespace Modules\AbsensiStaf\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Modules\AbsensiStaf\Models\IzinStaf;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class PengajuanIzin extends Page
{
    protected string $view = 'absensi-staf::filament.pages.pengajuan-izin';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static ?string $navigationLabel = 'Pengajuan Izin';
    protected static string|UnitEnum|null $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 4;

    // Form fields
    public string $jenis            = 'izin';
    public string $tanggal_mulai    = '';
    public string $tanggal_selesai  = '';
    public string $keterangan       = '';

    // Filter riwayat
    public string $filter_status = '';

    public function mount(): void
    {
        $this->tanggal_mulai   = today()->format('Y-m-d');
        $this->tanggal_selesai = today()->format('Y-m-d');
    }

    #[Computed]
    public function guru()
    {
        return auth()->user()->guru;
    }

    #[Computed]
    public function riwayatIzin(): \Illuminate\Support\Collection
    {
        if (!$this->guru) return collect();

        $query = IzinStaf::where('guru_id', $this->guru->id)
            ->orderBy('created_at', 'desc');

        if ($this->filter_status) {
            $query->where('status', $this->filter_status);
        }

        return $query->get();
    }

    #[Computed]
    public function jumlahHari(): int
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) return 0;
        return Carbon::parse($this->tanggal_mulai)
            ->diffInDays(Carbon::parse($this->tanggal_selesai)) + 1;
    }

    #[Computed]
    public function adaIzinAktif(): bool
    {
        if (!$this->guru) return false;

        return IzinStaf::where('guru_id', $this->guru->id)
            ->where('status', 'menunggu')
            ->exists();
    }

    public function ajukan(): void
    {
        $this->validate([
            'jenis'           => 'required|in:izin,sakit',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|min:10|max:500',
        ], [
            'tanggal_mulai.after_or_equal'   => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after_or_equal'  => 'Tanggal selesai harus setelah tanggal mulai.',
            'keterangan.min'                  => 'Keterangan minimal 10 karakter.',
        ]);

        if (!$this->guru) {
            Notification::make()
                ->title('Data guru tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        // Cek apakah tanggal bentrok dengan izin yang sudah ada
        $bentrok = IzinStaf::where('guru_id', $this->guru->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggal_mulai, $this->tanggal_selesai])
                  ->orWhereBetween('tanggal_selesai', [$this->tanggal_mulai, $this->tanggal_selesai])
                  ->orWhere(function ($q2) {
                      $q2->where('tanggal_mulai', '<=', $this->tanggal_mulai)
                         ->where('tanggal_selesai', '>=', $this->tanggal_selesai);
                  });
            })
            ->exists();

        if ($bentrok) {
            Notification::make()
                ->title('Tanggal bentrok')
                ->body('Sudah ada pengajuan izin pada rentang tanggal tersebut.')
                ->warning()
                ->send();
            return;
        }

        IzinStaf::create([
            'guru_id'         => $this->guru->id,
            'jenis'           => $this->jenis,
            'tanggal_mulai'   => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'keterangan'      => $this->keterangan,
            'status'          => 'menunggu',
        ]);

        Notification::make()
            ->title('Pengajuan berhasil!')
            ->body('Izin kamu sudah diajukan dan menunggu persetujuan admin.')
            ->success()
            ->send();

        $this->reset(['keterangan']);
        $this->tanggal_mulai   = today()->format('Y-m-d');
        $this->tanggal_selesai = today()->format('Y-m-d');
        $this->jenis           = 'izin';
    }

    public function batalkan(int $izinId): void
    {
        $izin = IzinStaf::where('guru_id', $this->guru?->id)
            ->where('id', $izinId)
            ->where('status', 'menunggu')
            ->first();

        if (!$izin) {
            Notification::make()
                ->title('Tidak bisa dibatalkan')
                ->body('Pengajuan sudah diproses oleh admin.')
                ->warning()
                ->send();
            return;
        }

        $izin->delete();

        Notification::make()
            ->title('Pengajuan dibatalkan')
            ->success()
            ->send();
    }
}
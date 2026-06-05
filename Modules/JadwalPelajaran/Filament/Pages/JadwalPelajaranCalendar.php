<?php

namespace Modules\JadwalPelajaran\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Modules\Kelas\Models\Kelas;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class JadwalPelajaranCalendar extends Page
{
    protected string $view = 'jadwal-pelajaran::filament.pages.jadwal-pelajaran-calendar';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;
    protected static ?string $navigationLabel = 'Kalender Jadwal';
    protected static string|UnitEnum|null $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 1;

    public ?int $kelas_id = null;
    public string $mode = 'mingguan';
    public string $hari_filter = 'Senin';

    #[Computed]
    public function kelasList(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }

    #[Computed]
    public function jamSlots(): array
    {
        return array_keys(JadwalPelajaran::JAM_SLOT);
    }

    #[Computed]
    public function hariList(): array
    {
        return array_keys(JadwalPelajaran::HARI);
    }

    #[Computed]
    public function jadwalGrid(): array
    {
        if (!$this->kelas_id) return ['grid' => [], 'occupied' => []];

        $query = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $this->kelas_id);

        if ($this->mode === 'harian') {
            $query->where('hari', $this->hari_filter);
        }

        $jadwals   = $query->get();
        $jamSlots  = array_keys(JadwalPelajaran::JAM_SLOT);
        $grid      = [];
        $occupied  = [];

        foreach ($jadwals as $jadwal) {
            $jamMulai   = substr($jadwal->jam_mulai, 0, 5);
            $jamSelesai = substr($jadwal->jam_selesai, 0, 5);

            $startIndex = array_search($jamMulai, $jamSlots);
            if ($startIndex === false) continue;

            // Hitung berapa slot yang dicakup
            $rowspan = 0;
            foreach ($jamSlots as $slot) {
                if ($slot >= $jamMulai && $slot < $jamSelesai) {
                    $rowspan++;
                }
            }
            if ($rowspan < 1) $rowspan = 1;

            $grid[$jadwal->hari][$jamMulai] = [
                'jadwal'  => $jadwal,
                'rowspan' => $rowspan,
            ];

            // Tandai slot yang dicakup rowspan agar tidak dirender ulang
            for ($i = $startIndex + 1; $i < $startIndex + $rowspan; $i++) {
                if (isset($jamSlots[$i])) {
                    $occupied[$jadwal->hari][$jamSlots[$i]] = true;
                }
            }
        }

        return ['grid' => $grid, 'occupied' => $occupied];
    }

    public function warnaKategori(?string $kategori): string
    {
        return match($kategori) {
            'Umum'            => 'bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
            'Agama'       => 'bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800',
            'Ekstrakurikuler' => 'bg-orange-100 text-orange-800 border border-orange-200 dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-800',
            default           => 'bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600',
        };
    }
}
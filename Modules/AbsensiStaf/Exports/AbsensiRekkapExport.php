<?php

namespace Modules\AbsensiStaf\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Modules\AbsensiStaf\Models\AbsensiStaf;
use Modules\Guru\Models\Guru;
use Carbon\Carbon;

class AbsensiRekkapExport implements WithMultipleSheets
{
    public function __construct(
        private string $bulan,
        private string $tahun,
    ) {}

    public function sheets(): array
    {
        // Sheet 1 — Rekap semua staf
        $sheets = [new AbsensiRekkapSheet($this->bulan, $this->tahun)];

        // Sheet per orang
        $gurus = Guru::orderBy('name')->get();
        foreach ($gurus as $guru) {
            $sheets[] = new AbsensiPersonalExport($guru->id, $this->bulan, $this->tahun);
        }

        return $sheets;
    }
}

// ─── Sheet rekap semua staf ───────────────────────────────────────────────────

class AbsensiRekkapSheet implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    public function __construct(
        private string $bulan,
        private string $tahun,
    ) {}

    public function collection()
    {
        $gurus = Guru::orderBy('name')->get();

        return $gurus->map(function (Guru $guru, int $index) {
            $absensi = AbsensiStaf::where('guru_id', $guru->id)
                ->whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)
                ->get();

            $hadir     = $absensi->where('status', 'hadir')->count();
            $terlambat = $absensi->where('status', 'terlambat')->count();
            $izin      = $absensi->where('status', 'izin')->count();
            $sakit     = $absensi->where('status', 'sakit')->count();
            $alpha     = $absensi->where('status', 'alpha')->count();

            // Total jam kerja
            $totalMenit = $absensi->sum(function ($abs) {
                if (!$abs->clock_in_at || !$abs->clock_out_at) return 0;
                return $abs->clock_in_at->diffInMinutes($abs->clock_out_at);
            });
            $totalJam  = intdiv($totalMenit, 60);
            $sisaMenit = $totalMenit % 60;

            // Rata-rata keterlambatan
            $terlambatData  = $absensi->where('status', 'terlambat');
            $avgTelat = $terlambatData->count() > 0
                ? round($terlambatData->avg(fn($a) => $a->telat ?? 0))
                : 0;

            return [
                'no'           => $index + 1,
                'nama'         => $guru->name,
                'hadir'        => $hadir,
                'terlambat'    => $terlambat,
                'izin'         => $izin,
                'sakit'        => $sakit,
                'alpha'        => $alpha,
                'total_hari'   => $absensi->count(),
                'total_jam'    => "{$totalJam}j {$sisaMenit}m",
                'avg_telat'    => $avgTelat > 0 ? $avgTelat . ' menit' : '-',
            ];
        });
    }

    public function headings(): array
    {
        $namaBulan = Carbon::create($this->tahun, $this->bulan)
            ->locale('id')
            ->translatedFormat('F Y');

        return [
            ['REKAP ABSENSI SELURUH STAF'],
            ['Periode: ' . $namaBulan],
            ['Dicetak: ' . now()->locale('id')->translatedFormat('l, d F Y H:i')],
            [''],
            [
                'No',
                'Nama Staf',
                'Hadir',
                'Terlambat',
                'Izin',
                'Sakit',
                'Alpha',
                'Total Hari',
                'Total Jam Kerja',
                'Rata-rata Keterlambatan',
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        $sheet->mergeCells('A4:J4');

        return [
            1 => [
                'font'      => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font'      => ['size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            3 => [
                'font'      => ['size' => 10, 'italic' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1e40af'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 28,
            'C' => 10,
            'D' => 12,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 12,
            'I' => 18,
            'J' => 24,
        ];
    }

    public function title(): string
    {
        return 'Rekap Semua Staf';
    }
}
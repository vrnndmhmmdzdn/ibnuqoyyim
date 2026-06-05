<?php

namespace Modules\AbsensiStaf\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Modules\AbsensiStaf\Models\AbsensiStaf;
use Modules\Guru\Models\Guru;
use Carbon\Carbon;

class AbsensiPersonalExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    WithColumnWidths
{
    public function __construct(
        private int $guruId,
        private string $bulan,
        private string $tahun,
    ) {}

    public function collection()
    {
        $guru    = Guru::find($this->guruId);
        $absensi = AbsensiStaf::where('guru_id', $this->guruId)
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal')
            ->get();

        return $absensi->map(function (AbsensiStaf $abs, int $index) {
            return [
                'no'          => $index + 1,
                'tanggal'     => $abs->tanggal->locale('id')->translatedFormat('l, d M Y'),
                'clock_in'    => $abs->clock_in_at?->format('H:i') ?? '-',
                'clock_out'   => $abs->clock_out_at?->format('H:i') ?? '-',
                'durasi'      => $abs->durasi ?? '-',
                'telat'       => $abs->telat > 0 ? $abs->telat . ' menit' : '-',
                'status'      => AbsensiStaf::STATUS[$abs->status] ?? $abs->status,
                'keterangan'  => $abs->keterangan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        $guru      = Guru::find($this->guruId);
        $namaBulan = Carbon::create($this->tahun, $this->bulan)->locale('id')->translatedFormat('F Y');

        return [
            ['REKAP ABSENSI - ' . strtoupper($guru?->name ?? '-')],
            ['Periode: ' . $namaBulan],
            [''],
            [
                'No',
                'Tanggal',
                'Jam Masuk',
                'Jam Pulang',
                'Durasi',
                'Keterlambatan',
                'Status',
                'Keterangan',
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Judul
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        return [
            1 => [
                'font'      => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font'      => ['size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            4 => [
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
            'B' => 30,
            'C' => 12,
            'D' => 12,
            'E' => 12,
            'F' => 16,
            'G' => 14,
            'H' => 30,
        ];
    }

    public function title(): string
    {
        $guru = Guru::find($this->guruId);
        return substr($guru?->name ?? 'Staf', 0, 31);
    }
}
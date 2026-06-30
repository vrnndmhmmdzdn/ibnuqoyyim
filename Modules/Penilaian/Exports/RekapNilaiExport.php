<?php

namespace Modules\Penilaian\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Modules\Kelas\Models\Kelas;
use Modules\TahunAjaran\Models\TahunAjaran;
use Carbon\Carbon;

class RekapNilaiExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    WithColumnWidths,
    WithEvents
{
    private \Illuminate\Support\Collection $siswaList;
    private \Illuminate\Support\Collection $mapelList;
    private array $rekapData;
    private int $headerRow;

    public function __construct(
        private ?int $kelasId,
        private ?int $tahunAjaranId,
        private string $semester,
        \Illuminate\Support\Collection $siswaList,
        \Illuminate\Support\Collection $mapelList,
        array $rekapData,
    ) {
        $this->siswaList = $siswaList;
        $this->mapelList = $mapelList;
        $this->rekapData = $rekapData;
        $this->headerRow = 5; // baris header tabel (setelah 3 baris judul + 1 kosong)
    }

    public function collection()
    {
        return $this->siswaList->values()->map(function ($siswa, int $index) {
            $row = [
                'no'   => $index + 1,
                'nama' => $siswa->nama_lengkap,
                'nis'  => $siswa->nis,
            ];

            $totalNilai = [];

            foreach ($this->mapelList as $mapel) {
                $rekap = $this->rekapData[$siswa->id][$mapel->id] ?? null;

                if ($rekap && $rekap->nilai_akhir !== null) {
                    $row['mapel_' . $mapel->id] = number_format($rekap->nilai_akhir, 1);
                    $row['predikat_' . $mapel->id] = $rekap->predikat ?? '-';
                    $totalNilai[] = $rekap->nilai_akhir;
                } else {
                    $row['mapel_' . $mapel->id] = '-';
                    $row['predikat_' . $mapel->id] = '-';
                }
            }

            $row['rata_rata'] = count($totalNilai) > 0
                ? number_format(array_sum($totalNilai) / count($totalNilai), 1)
                : '-';

            return $row;
        });
    }

    public function headings(): array
    {
        $kelas       = Kelas::find($this->kelasId);
        $tahunAjaran = TahunAjaran::find($this->tahunAjaranId);

        $heading1 = ['REKAP NILAI SISWA'];
        $heading2 = [
            'Kelas: ' . ($kelas?->nama_kelas ?? '-')
            . '  |  Tahun Ajaran: ' . ($tahunAjaran?->tahun_ajaran ?? '-')
            . '  |  Semester: ' . $this->semester,
        ];
        $heading3 = ['Dicetak: ' . now()->locale('id')->translatedFormat('l, d F Y H:i')];
        $heading4 = [''];

        $tableHeader = ['No', 'Nama Siswa', 'NIS'];
        foreach ($this->mapelList as $mapel) {
            $tableHeader[] = $mapel->pelajaran;
            $tableHeader[] = 'Predikat';
        }
        $tableHeader[] = 'Rata-rata';

        return [
            $heading1,
            $heading2,
            $heading3,
            $heading4,
            $tableHeader,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $totalKolom = 3 + ($this->mapelList->count() * 2) + 1;
        $lastCol    = $this->numberToColumnLetter($totalKolom);

        // Merge judul
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->mergeCells("A4:{$lastCol}4");

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
            $this->headerRow => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '16a34a'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,   // No
            'B' => 28,  // Nama
            'C' => 14,  // NIS
        ];

        $col = 4; // mulai dari kolom D
        foreach ($this->mapelList as $mapel) {
            $widths[$this->numberToColumnLetter($col)]     = 14; // nilai mapel
            $widths[$this->numberToColumnLetter($col + 1)] = 10; // predikat
            $col += 2;
        }

        $widths[$this->numberToColumnLetter($col)] = 12; // rata-rata

        return $widths;
    }

    public function title(): string
    {
        $kelas = Kelas::find($this->kelasId);
        $title = 'Rekap ' . ($kelas?->nama_kelas ?? 'Kelas') . ' Smt ' . $this->semester;
        return substr($title, 0, 31);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $totalKolom = 3 + ($this->mapelList->count() * 2) + 1;
                $lastCol    = $this->numberToColumnLetter($totalKolom);
                $lastRow    = $this->headerRow + $this->siswaList->count();

                // Border untuk seluruh tabel data
                $sheet->getStyle("A{$this->headerRow}:{$lastCol}{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('CCCCCC'));

                // Center semua kolom nilai dan predikat
                $sheet->getStyle("D" . ($this->headerRow + 1) . ":{$lastCol}{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Bold kolom rata-rata
                $sheet->getStyle("{$lastCol}" . ($this->headerRow + 1) . ":{$lastCol}{$lastRow}")
                    ->getFont()
                    ->setBold(true);

                // Freeze panes — header dan kolom nama tetap terlihat saat scroll
                $sheet->freezePane('D' . ($this->headerRow + 1));
            },
        ];
    }

    private function numberToColumnLetter(int $num): string
    {
        $letter = '';
        while ($num > 0) {
            $mod    = ($num - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $num    = intdiv($num - $mod, 26);
        }
        return $letter;
    }
}
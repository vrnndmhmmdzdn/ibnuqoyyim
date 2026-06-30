<?php

namespace Modules\MutabaahTahfidz\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Computed;
use Modules\Kelas\Models\Kelas;
use Modules\MutabaahTahfidz\Models\MutabaahRecord;
use Modules\Siswa\Models\Siswa;
use UnitEnum;

class MutabaahLaporan extends Page
{
    protected string $view = 'mutabaah-tahfidz::filament.pages.mutabaah-laporan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;
    protected static ?string $navigationLabel  = 'Laporan';
    protected static string|UnitEnum|null $navigationGroup = 'Mutabaah Tahfidz';
    protected static ?int $navigationSort      = 3;

    public ?int   $kelas_id  = null;
    public string $mode      = 'pekanan';
    public string $weekStart = '';
    public int    $bulan     = 1;
    public int    $tahun     = 2025;

    const HARI_LABEL = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Ahd'];
    const HARI_FULL  = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
    const BULAN_LABEL = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November',12=>'Desember',
    ];

    public function mount(): void
    {
        $this->weekStart = $this->getMondayStr(today());
        $this->bulan     = (int) today()->format('n');
        $this->tahun     = (int) today()->format('Y');
    }

    // ── Date helpers ──────────────────────────────────────────────────

    private function getMondayStr(Carbon $date): string
    {
        $day = $date->dayOfWeek;
        return $date->copy()->subDays($day === 0 ? 6 : $day - 1)->format('Y-m-d');
    }

    public function getWeekDates(): array
    {
        $mon = Carbon::parse($this->weekStart . 'T00:00:00');
        return array_map(fn ($i) => $mon->copy()->addDays($i)->format('Y-m-d'), range(0, 6));
    }

    public function getWeekLabel(): string
    {
        $d = $this->getWeekDates();
        return Carbon::parse($d[0])->locale('id')->translatedFormat('d M')
             . ' – '
             . Carbon::parse($d[6])->locale('id')->translatedFormat('d M Y');
    }

    public function getMonthDates(): array
    {
        $start   = Carbon::create($this->tahun, $this->bulan, 1);
        $end     = $start->copy()->endOfMonth();
        $dates   = [];
        $current = $start->copy();
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }
        return $dates;
    }

    public function getMonthLabel(): string
    {
        return (self::BULAN_LABEL[$this->bulan] ?? '') . ' ' . $this->tahun;
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function kelasList(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }

    #[Computed]
    public function tahunList(): array
    {
        $current = (int) today()->format('Y');
        return array_combine(range($current - 2, $current + 1), range($current - 2, $current + 1));
    }

    #[Computed]
    public function weekData(): array
    {
        if (!$this->kelas_id || $this->mode !== 'pekanan') return [];

        $dates     = $this->getWeekDates();
        $siswaList = $this->getSiswaList();
        if ($siswaList->isEmpty()) return [];

        $records = $this->fetchRecords($dates[0], $dates[6], $siswaList);
        $grouped = $records->groupBy('siswa_id');

        $result = [];
        foreach ($siswaList as $siswa) {
            [$dayData, $sAyat, $sSetoran, $sTidak] = $this->buildDayData(
                $grouped[$siswa->id] ?? collect(), $dates
            );
            $result[] = compact('siswa', 'dayData') + [
                'totalAyat' => $sAyat,
                'setoran'   => $sSetoran,
                'tidak'     => $sTidak,
                'belum'     => count(array_filter($dayData, fn ($r) => $r === null)),
            ];
        }

        usort($result, fn ($a, $b) => $b['totalAyat'] <=> $a['totalAyat']);

        return [
            'rows'       => $result,
            'dates'      => $dates,
            'totalAyat'  => array_sum(array_column($result, 'totalAyat')),
            'totalSetor' => array_sum(array_column($result, 'setoran')),
            'totalSiswa' => count($result),
        ];
    }

    #[Computed]
    public function monthData(): array
    {
        if (!$this->kelas_id || $this->mode !== 'bulanan') return [];

        $dates     = $this->getMonthDates();
        $siswaList = $this->getSiswaList();
        if ($siswaList->isEmpty()) return [];

        $records = $this->fetchRecords($dates[0], $dates[count($dates) - 1], $siswaList);
        $grouped = $records->groupBy('siswa_id');

        $result = [];
        foreach ($siswaList as $siswa) {
            [$dayData, $sAyat, $sSetoran, $sTidak] = $this->buildDayData(
                $grouped[$siswa->id] ?? collect(), $dates
            );
            $perMinggu = $this->buildWeeklySummary($dayData, $dates);

            $result[] = compact('siswa', 'dayData', 'perMinggu') + [
                'totalAyat' => $sAyat,
                'setoran'   => $sSetoran,
                'tidak'     => $sTidak,
                'belum'     => count(array_filter($dayData, fn ($r) => $r === null)),
            ];
        }

        usort($result, fn ($a, $b) => $b['totalAyat'] <=> $a['totalAyat']);

        return [
            'rows'        => $result,
            'dates'       => $dates,
            'totalAyat'   => array_sum(array_column($result, 'totalAyat')),
            'totalSetor'  => array_sum(array_column($result, 'setoran')),
            'totalSiswa'  => count($result),
            'daysInMonth' => count($dates),
        ];
    }

    // ── Private helpers ───────────────────────────────────────────────

    /**
     * Ambil siswa berdasarkan kelas_pivot, bukan dari mutabaah_records.
     * Ini yang memastikan semua siswa di kelas tampil, termasuk yang belum setoran.
     */
    private function getSiswaList(): \Illuminate\Support\Collection
    {
        return Siswa::whereHas('kelas', fn ($q) =>
            $q->where('kelas.id', $this->kelas_id)
              ->where('kelas_pivot.is_aktif', true)
              ->whereNull('kelas_pivot.deleted_at')
        )->orderBy('nama_lengkap')->get();
    }

    /**
     * Ambil records berdasarkan siswa_id (bukan kelas_id),
     * karena kelas_id di records bisa berbeda dengan pivot saat data dummy.
     */
    private function fetchRecords(string $from, string $to, \Illuminate\Support\Collection $siswaList): \Illuminate\Support\Collection
    {
        return MutabaahRecord::with(['surah'])
            ->whereIn('siswa_id', $siswaList->pluck('id'))
            ->whereBetween('tanggal', [$from, $to])
            ->get();
    }

    private function buildDayData(\Illuminate\Support\Collection $siswaRecs, array $dates): array
    {
        $dayData  = [];
        $sAyat    = 0;
        $sSetoran = 0;
        $sTidak   = 0;

        foreach ($dates as $date) {
            $rec = $siswaRecs->first(fn ($r) => $r->tanggal->format('Y-m-d') === $date);
            $dayData[$date] = $rec;
            if ($rec) {
                $sAyat += $rec->jumlah_ayat;
                if (!in_array($rec->status, ['tidak_setoran', 'tidak_masuk'])) {
                    $sSetoran++;
                } elseif ($rec->status === 'tidak_setoran') {
                    $sTidak++;
                }
            }
        }

        return [$dayData, $sAyat, $sSetoran, $sTidak];
    }

    private function buildWeeklySummary(array $dayData, array $dates): array
    {
        $weeks = [];
        $wNum  = 1;
        foreach (array_chunk($dates, 7) as $weekDates) {
            $ayat  = 0;
            $setor = 0;
            foreach ($weekDates as $d) {
                $rec = $dayData[$d] ?? null;
                if ($rec) {
                    $ayat += $rec->jumlah_ayat;
                    if (!in_array($rec->status, ['tidak_setoran', 'tidak_masuk'])) {
                        $setor++;
                    }
                }
            }
            $weeks[] = ['label' => "Minggu {$wNum}", 'ayat' => $ayat, 'setor' => $setor];
            $wNum++;
        }
        return $weeks;
    }

    // ── Actions ───────────────────────────────────────────────────────

    public function shiftWeek(int $direction): void
    {
        $this->weekStart = Carbon::parse($this->weekStart . 'T00:00:00')
            ->addDays($direction * 7)->format('Y-m-d');
        unset($this->weekData);
    }

    public function shiftMonth(int $direction): void
    {
        $date        = Carbon::create($this->tahun, $this->bulan, 1)->addMonths($direction);
        $this->bulan = (int) $date->format('n');
        $this->tahun = (int) $date->format('Y');
        unset($this->monthData);
    }

    public function updatedKelasId(): void { unset($this->weekData, $this->monthData); }
    public function updatedMode(): void    { unset($this->weekData, $this->monthData); }
    public function updatedBulan(): void   { unset($this->monthData); }
    public function updatedTahun(): void   { unset($this->monthData); }

    // ── Export CSV Pekanan ─────────────────────────────────────────────

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data  = $this->weekData;
        $dates = $data['dates'] ?? $this->getWeekDates();
        $kelas = Kelas::find($this->kelas_id)?->nama_kelas ?? 'Kelas';

        return response()->streamDownload(function () use ($data, $dates, $kelas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ["LAPORAN MUTABAAH TAHFIDZ - {$kelas}"]);
            fputcsv($out, ['Periode: ' . $this->getWeekLabel()]);
            fputcsv($out, []);

            $header = ['No', 'Nama Siswa'];
            foreach (self::HARI_LABEL as $h) {
                $header[] = $h . ' (Status)';
                $header[] = $h . ' (Ayat)';
            }
            $header[] = 'Total Ayat';
            $header[] = 'Hari Setoran';
            fputcsv($out, $header);

            foreach (($data['rows'] ?? []) as $i => $row) {
                $line = [$i + 1, $row['siswa']->nama_lengkap];
                foreach ($dates as $date) {
                    $rec    = $row['dayData'][$date] ?? null;
                    $line[] = $rec ? MutabaahRecord::STATUS[$rec->status] : '-';
                    $line[] = $rec?->jumlah_ayat ?? 0;
                }
                $line[] = $row['totalAyat'];
                $line[] = $row['setoran'];
                fputcsv($out, $line);
            }
            fclose($out);
        }, "Laporan_Pekanan_{$kelas}_{$this->weekStart}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ── Export CSV Bulanan ─────────────────────────────────────────────

    public function exportCsvBulanan(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data  = $this->monthData;
        $dates = $data['dates'] ?? $this->getMonthDates();
        $kelas = Kelas::find($this->kelas_id)?->nama_kelas ?? 'Kelas';

        return response()->streamDownload(function () use ($data, $dates, $kelas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ["LAPORAN MUTABAAH TAHFIDZ BULANAN - {$kelas}"]);
            fputcsv($out, ['Periode: ' . $this->getMonthLabel()]);
            fputcsv($out, []);

            $header = ['No', 'Nama Siswa'];
            foreach ($dates as $date) {
                $d        = Carbon::parse($date);
                $hIdx     = $d->dayOfWeek === 0 ? 6 : $d->dayOfWeek - 1;
                $header[] = $d->format('d') . ' ' . (self::HARI_LABEL[$hIdx] ?? '');
            }
            $header[] = 'Total Ayat';
            $header[] = 'Hari Setoran';
            $header[] = 'Tidak Setoran';
            fputcsv($out, $header);

            foreach (($data['rows'] ?? []) as $i => $row) {
                $line = [$i + 1, $row['siswa']->nama_lengkap];
                foreach ($dates as $date) {
                    $rec    = $row['dayData'][$date] ?? null;
                    $line[] = $rec
                        ? MutabaahRecord::statusEmoji($rec->status) . ($rec->jumlah_ayat > 0 ? ' ' . $rec->jumlah_ayat . 'A' : '')
                        : '';
                }
                $line[] = $row['totalAyat'];
                $line[] = $row['setoran'];
                $line[] = $row['tidak'];
                fputcsv($out, $line);
            }

            // Total row
            $totals = ['', 'TOTAL'];
            foreach ($dates as $date) {
                $dayTotal = collect($data['rows'] ?? [])->sum(fn ($r) => $r['dayData'][$date]?->jumlah_ayat ?? 0);
                $totals[] = $dayTotal > 0 ? $dayTotal : '';
            }
            $totals[] = $data['totalAyat'] ?? 0;
            $totals[] = $data['totalSetor'] ?? 0;
            $totals[] = '';
            fputcsv($out, $totals);
            fclose($out);
        }, "Laporan_Bulanan_{$kelas}_{$this->tahun}_{$this->bulan}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

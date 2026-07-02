<?php

namespace Modules\JurnalGuru\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Modules\JurnalGuru\Models\JurnalGuru;
use Modules\Guru\Models\Guru;
use Modules\Kelas\Models\Kelas;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class JurnalCalendar extends Page
{
    protected string $view = 'jurnal-guru::filament.pages.jurnal-calendar';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;
    protected static ?string $navigationLabel = 'Kalender Jurnal';
    protected static string|UnitEnum|null $navigationGroup = 'Jurnal';
    protected static ?int $navigationSort = 1;

    // Filter state
    public ?int $guru_id   = null;
    public ?int $kelas_id  = null;
    public string $capaian = '';

    #[Computed]
    public function guruList(): array
    {
        return Guru::orderBy('name')->pluck('name', 'id')->toArray();
    }

    #[Computed]
    public function kelasList(): array
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id')->toArray();
    }

    #[Computed]
    public function capaianList(): array
    {
        return JurnalGuru::CAPAIAN;
    }

    #[Computed]
    public function jurnals(): array
    {
        $user = auth()->user();

        $query = JurnalGuru::with(['guru', 'kelas', 'mataPelajaran', 'lampirans'])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai');

        $selectedGuruId = $this->guru_id ? (int) $this->guru_id : null;
        $selectedKelasId = $this->kelas_id ? (int) $this->kelas_id : null;
        $selectedCapaian = trim($this->capaian);

        if($user && $user->role !== 'admin'){
            $guru = Guru::where('user_id', $user->id)->first();
            $query->where('guru_id', $guru?->id ?? 0);
        }else{
            if($selectedGuruId){
                $query->where('guru_id', $selectedGuruId);
            }
        }

        if ($selectedKelasId){
            $query->where('kelas_id', $selectedKelasId);
        }

        if ($selectedCapaian !== '') {
            $query->where('capaian', $selectedCapaian);
        }

        return $query->get()
            ->map(function (JurnalGuru $jurnal) {
                $warna = JurnalGuru::warnaCapaian($jurnal->capaian);

                return [
                    'id'              => $jurnal->id,
                    'title'           => substr($jurnal->jam_mulai, 0, 5) . ' ' . ($jurnal->mataPelajaran?->pelajaran ?? '-'),
                    'start'           => $jurnal->tanggal->format('Y-m-d') . 'T' . substr($jurnal->jam_mulai, 0, 5),
                    'end'             => $jurnal->tanggal->format('Y-m-d') . 'T' . substr($jurnal->jam_selesai, 0, 5),
                    'backgroundColor' => $warna['bg'],
                    'borderColor'     => $warna['border'],
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'guru'              => $jurnal->guru?->name ?? '-',
                        'kelas'             => $jurnal->kelas?->nama_kelas ?? '-',
                        'mata_pelajaran'    => $jurnal->mataPelajaran?->pelajaran ?? '-',
                        'tanggal'           => $jurnal->tanggal->locale('id')->translatedFormat('l, d F Y'),
                        'jam_mulai'         => substr($jurnal->jam_mulai, 0, 5),
                        'jam_selesai'       => substr($jurnal->jam_selesai, 0, 5),
                        'pertemuan_ke'      => $jurnal->pertemuan_ke ?? '-',
                        'materi'            => $jurnal->materi,
                        'kompetensi_dasar'  => $jurnal->kompetensi_dasar,
                        'deskripsi_kegiatan' => $jurnal->deskripsi_kegiatan,
                        'metode'            => JurnalGuru::METODE[$jurnal->metode_pembelajaran] ?? '-',
                        'media'             => $jurnal->media_pembelajaran ?? '-',
                        'jumlah_hadir'      => $jurnal->jumlah_hadir,
                        'jumlah_tidak_hadir' => $jurnal->jumlah_tidak_hadir,
                        'total_siswa'       => $jurnal->total_siswa,
                        'persentase_hadir'  => $jurnal->persentase_hadir,
                        'capaian'           => JurnalGuru::CAPAIAN[$jurnal->capaian] ?? '-',
                        'capaian_raw'       => $jurnal->capaian,
                        'tindak_lanjut'     => $jurnal->tindak_lanjut ?? '-',
                        'catatan'           => $jurnal->catatan ?? '-',
                        'status'            => JurnalGuru::STATUS[$jurnal->status] ?? '-',
                        'lampirans'         => $jurnal->lampirans->map(function ($lampiran) {
                            $ekstensi = strtolower(pathinfo($lampiran->nama_file, PATHINFO_EXTENSION));
                            return [
                                'id'        => $lampiran->id,
                                'nama_file' => $lampiran->nama_file,
                                'tipe'      => \Modules\JurnalGuru\Models\JurnalLampiran::TIPE[$lampiran->tipe] ?? $lampiran->tipe,
                                'url'       => \Illuminate\Support\Facades\Storage::url($lampiran->path), // Mengambil URL publik
                                'is_image'  => in_array($ekstensi, ['jpg', 'jpeg', 'png', 'webp']),
                                'is_pdf'    => $ekstensi === 'pdf',
                            ];
                        })->toArray(),
                    ],
                ];
            })
            ->toArray();
    }
    public function updatedGuruId(): void
    {
        $this->dispatch('jurnals-updated', $this->jurnals);
    }

    public function updatedKelasId(): void
    {
        $this->dispatch('jurnals-updated', $this->jurnals);
    }

    public function updatedCapaian(): void
    {
        $this->dispatch('jurnals-updated', $this->jurnals);
    }
}

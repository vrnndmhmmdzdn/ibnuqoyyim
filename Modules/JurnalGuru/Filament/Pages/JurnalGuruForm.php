<?php

namespace Modules\JurnalGuru\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Modules\JurnalGuru\Models\JurnalGuru;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;
use Modules\Guru\Models\Guru;
use Carbon\Carbon;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Livewire\WithFileUploads;

class JurnalGuruForm extends Page
{
    protected string $view = 'jurnal-guru::filament.pages.jurnal-guru-form';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;
    protected static ?string $navigationLabel = 'Input Jurnal';
    protected static string|UnitEnum|null $navigationGroup = 'Jurnal';
    protected static ?int $navigationSort = 2;

    // Step 1 — pilih tanggal
    public string $tanggal = '';

    // Step 2 — pilih jadwal atau manual
    public bool $mode_manual = false;
    public ?int $selected_jadwal_id = null;

    // Form fields
    public ?int    $guru_id           = null;
    public ?int    $kelas_id          = null;
    public ?int    $mata_pelajaran_id = null;
    public ?int    $tahun_ajaran_id   = null;
    public string  $jam_mulai         = '';
    public string  $jam_selesai       = '';
    public ?int    $pertemuan_ke      = null;
    public string  $materi            = '';
    public string  $kompetensi_dasar  = '';
    public string  $deskripsi_kegiatan = '';
    public string  $metode_pembelajaran = '';
    public string  $media_pembelajaran = '';
    public int     $jumlah_hadir      = 0;
    public int     $jumlah_tidak_hadir = 0;
    public string  $capaian           = 'tercapai';
    public string  $tindak_lanjut     = '';
    public string  $catatan           = '';
    public string  $status            = 'draft';

    public function mount(): void
    {
        $this->tanggal        = today()->format('Y-m-d');
        $this->tahun_ajaran_id = TahunAjaran::where('is_aktif', true)->first()?->id;
        $this->guru_id        = auth()->user()->guru?->id;
    }

    #[Computed]
    public function jadwalHariIni(): \Illuminate\Support\Collection
    {
        if (!$this->tanggal || !$this->guru_id) return collect();

        $namaHari = Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l');

        // Map nama hari Indonesia ke enum
        $hariMap = [
            'Senin'  => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu'   => 'Rabu',
            'Kamis'  => 'Kamis',
            'Jumat'  => 'Jumat',
            'Sabtu'  => 'Sabtu',
        ];

        $hari = $hariMap[$namaHari] ?? null;
        if (!$hari) return collect();

        return JadwalPelajaran::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', $this->guru_id)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();
    }

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
    public function mapelList(): array
    {
        return MataPelajaran::where('is_aktif', true)
            ->orderBy('pelajaran')
            ->pluck('pelajaran', 'id')
            ->toArray();
    }

    public function updatedTanggal(): void
    {
        // Reset pilihan jadwal ketika tanggal berubah
        $this->reset([
            'selected_jadwal_id', 'mode_manual',
            'kelas_id', 'mata_pelajaran_id',
            'jam_mulai', 'jam_selesai', 'pertemuan_ke',
        ]);
    }

    public function pilihJadwal(int $jadwalId): void
    {
        $jadwal = JadwalPelajaran::with(['kelas', 'mataPelajaran'])->find($jadwalId);
        if (!$jadwal) return;

        $this->selected_jadwal_id = $jadwalId;
        $this->mode_manual        = false;

        // Auto-fill dari jadwal
        $this->kelas_id          = $jadwal->kelas_id;
        $this->mata_pelajaran_id = $jadwal->mata_pelajaran_id;
        $this->jam_mulai         = substr($jadwal->jam_mulai, 0, 5);
        $this->jam_selesai       = substr($jadwal->jam_selesai, 0, 5);

        // Hitung pertemuan_ke otomatis
        $this->pertemuan_ke = JurnalGuru::where('guru_id', $this->guru_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
            ->count() + 1;
    }

    public function inputManual(): void
    {
        $this->mode_manual        = true;
        $this->selected_jadwal_id = null;
        $this->reset([
            'kelas_id', 'mata_pelajaran_id',
            'jam_mulai', 'jam_selesai', 'pertemuan_ke',
        ]);
    }

    public function updatedKelasId(): void
    {
        $this->hitungPertemuan();
    }

    public function updatedMataPelajaranId(): void
    {
        $this->hitungPertemuan();
    }

    private function hitungPertemuan(): void
    {
        if (!$this->guru_id || !$this->kelas_id || !$this->mata_pelajaran_id) return;

        $this->pertemuan_ke = JurnalGuru::where('guru_id', $this->guru_id)
            ->where('kelas_id', $this->kelas_id)
            ->where('mata_pelajaran_id', $this->mata_pelajaran_id)
            ->count() + 1;
    }
    use WithFileUploads;

    // Tambah property
    public $lampiran_file = null;
    public ?int $jurnal_id_tersimpan = null;

// Tambah setelah method simpan() — update return
    public function simpan(string $statusInput): void
    {
        $this->validate([
            'tanggal'             => 'required|date',
            'guru_id'             => 'required|exists:gurus,id',
            'kelas_id'            => 'required|exists:kelas,id',
            'mata_pelajaran_id'   => 'required|exists:mata_pelajarans,id',
            'jam_mulai'           => 'required',
            'jam_selesai'         => 'required',
            'materi'              => 'required|string|max:255',
            'kompetensi_dasar'    => 'required|string',
            'deskripsi_kegiatan'  => 'required|string',
            'metode_pembelajaran' => 'required|string',
            'jumlah_hadir'        => 'required|integer|min:0',
            'jumlah_tidak_hadir'  => 'required|integer|min:0',
            'capaian'             => 'required|in:tercapai,sebagian,belum',
        ]);

        $sudahAda = JurnalGuru::where('guru_id', $this->guru_id)
            ->where('kelas_id', $this->kelas_id)
            ->where('mata_pelajaran_id', $this->mata_pelajaran_id)
            ->whereDate('tanggal', $this->tanggal)
            ->exists();

        if ($sudahAda) {
            Notification::make()
                ->title('Jurnal sudah ada!')
                ->body('Jurnal untuk kombinasi ini sudah pernah diisi pada tanggal tersebut.')
                ->warning()
                ->send();
            return;
        }

        $jurnal = JurnalGuru::create([
            'guru_id'             => $this->guru_id,
            'kelas_id'            => $this->kelas_id,
            'mata_pelajaran_id'   => $this->mata_pelajaran_id,
            'tahun_ajaran_id'     => $this->tahun_ajaran_id,
            'tanggal'             => $this->tanggal,
            'jam_mulai'           => $this->jam_mulai,
            'jam_selesai'         => $this->jam_selesai,
            'pertemuan_ke'        => $this->pertemuan_ke,
            'materi'              => $this->materi,
            'kompetensi_dasar'    => $this->kompetensi_dasar,
            'deskripsi_kegiatan'  => $this->deskripsi_kegiatan,
            'metode_pembelajaran' => $this->metode_pembelajaran,
            'media_pembelajaran'  => $this->media_pembelajaran ?: null,
            'jumlah_hadir'        => $this->jumlah_hadir,
            'jumlah_tidak_hadir'  => $this->jumlah_tidak_hadir,
            'capaian'             => $this->capaian,
            'tindak_lanjut'       => $this->tindak_lanjut ?: null,
            'catatan'             => $this->catatan ?: null,
            'status'              => $statusInput,
            'submitted_at'        => $statusInput === 'submitted' ? now() : null,
        ]);

        // Simpan id jurnal untuk section lampiran
        $this->jurnal_id_tersimpan = $jurnal->id;

        Notification::make()
            ->title($statusInput === 'submitted' ? 'Jurnal berhasil disubmit!' : 'Draft jurnal tersimpan!')
            ->body('Silakan lampirkan file pendukung di bawah ini.')
            ->success()
            ->send();

        $this->reset([
            'selected_jadwal_id', 'mode_manual',
            'kelas_id', 'mata_pelajaran_id',
            'jam_mulai', 'jam_selesai', 'pertemuan_ke',
            'materi', 'kompetensi_dasar', 'deskripsi_kegiatan',
            'metode_pembelajaran', 'media_pembelajaran',
            'jumlah_hadir', 'jumlah_tidak_hadir',
            'capaian', 'tindak_lanjut', 'catatan',
        ]);

        $this->capaian = 'tercapai';
        $this->status  = 'draft';
    }

    public function uploadLampiran(): void
    {
        $this->validate([
            'lampiran_file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,xlsm,ppt,pptx|max:10240',
        ]);

        $jurnal = JurnalGuru::find($this->jurnal_id_tersimpan);
        if (!$jurnal) return;

        $file      = $this->lampiran_file;
        $ekstensi  = $file->getClientOriginalExtension();
        $namaFile  = $file->getClientOriginalName();
        $path      = $file->store('jurnal-lampirans/' . $jurnal->id, 'local');

        JurnalLampiran::create([
            'jurnal_guru_id' => $jurnal->id,
            'nama_file'      => $namaFile,
            'path'           => $path,
            'tipe'           => JurnalLampiran::deteksiTipe($ekstensi),
            'ukuran'         => $file->getSize(),
        ]);

        $this->reset('lampiran_file');

        Notification::make()
            ->title('Lampiran berhasil ditambahkan')
            ->success()
            ->send();
    }

    public function hapusLampiran(int $lampiranId): void
    {
        $lampiran = JurnalLampiran::find($lampiranId);
        if (!$lampiran) return;

        Storage::disk('local')->delete($lampiran->path);
        $lampiran->delete();

        Notification::make()
            ->title('Lampiran dihapus')
            ->success()
            ->send();
    }

    #[Computed]
    public function lampiranJurnal(): \Illuminate\Support\Collection
    {
        if (!$this->jurnal_id_tersimpan) return collect();
        return JurnalLampiran::where('jurnal_guru_id', $this->jurnal_id_tersimpan)->get();
    }
}
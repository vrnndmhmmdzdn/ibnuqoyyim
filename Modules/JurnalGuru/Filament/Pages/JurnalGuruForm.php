<?php

namespace Modules\JurnalGuru\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Modules\Guru\Models\Guru;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Modules\JurnalGuru\Models\JurnalGuru;
use Modules\JurnalGuru\Models\JurnalLampiran;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class JurnalGuruForm extends Page
{
    // Mengaktifkan fitur upload file di Livewire
    use WithFileUploads;

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
    public ?int    $pertemuan_ke      = 1;
    public string  $jam_mulai         = '';
    public string  $jam_selesai       = '';
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

    // File Lampiran Tambahan Saat Input Form Utama
    public $lampiran_file;

    // Upload & Success State (untuk manajemen setelah jurnal tersimpan)
    public ?int $jurnal_id_tersimpan = null;

    public function mount(): void
    {
        $this->tanggal = today()->format('Y-m-d');
        
        // Auto-assign tahun ajaran aktif jika ada
        $aktif = TahunAjaran::where('is_aktif', true)->first();
        if ($aktif) {
            $this->tahun_ajaran_id = $aktif->id;
        }

        // Jika user yang login terikat dengan data Guru, kunci guru_id ke dirinya sendiri
        $user = auth()->user();
        if ($user) {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($guru) {
                $this->guru_id = $guru->id;
            }
        }
    }

    #[Computed]
    public function jadwals()
    {
        if (!$this->tanggal) {
            return collect();
        }

        $user = auth()->user();
        $query = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])
            ->where('hari', $this->namaHariIndo);

        // PERBAIKAN FILTER: Jika user yang login terdaftar sebagai Guru, 
        // MAKA WAJIB hanya menampilkan jadwal miliknya sendiri.
        if ($user) {
            $guru = Guru::where('user_id', $user->id)->first();
            if ($guru) {
                $query->where('guru_id', $guru->id);
            }
        }

        return $query->orderBy('jam_mulai')->get();
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
        return MataPelajaran::orderBy('pelajaran')->pluck('pelajaran', 'id')->toArray();
    }

    #[Computed]
    public function lampirans()
    {
        if (!$this->jurnal_id_tersimpan) {
            return collect();
        }
        return JurnalLampiran::where('jurnal_guru_id', $this->jurnal_id_tersimpan)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getNamaHariIndoProperty(): string
    {
        return Carbon::parse($this->tanggal)->locale('id')->translatedFormat('l'); // 'Senin', 'Selasa', dll
    }

    public function updatedTanggal(): void
    {
        $this->resetFormLaporan();
        $this->selected_jadwal_id = null;
        $this->mode_manual = false;
    }

    public function inputManual(): void
    {
        $this->mode_manual = !$this->mode_manual;
        $this->selected_jadwal_id = null;
        $this->resetFormLaporan();
    }

    public function pilihJadwal(int $jadwalId): void
    {
        $this->selected_jadwal_id = $jadwalId;
        $this->mode_manual = false;

        $jadwal = JadwalPelajaran::find($jadwalId);
        if ($jadwal) {
            $this->guru_id           = $jadwal->guru_id;
            $this->kelas_id          = $jadwal->kelas_id;
            $this->mata_pelajaran_id = $jadwal->mata_pelajaran_id;
            $this->jam_mulai         = substr($jadwal->jam_mulai, 0, 5);
            $this->jam_selesai       = substr($jadwal->jam_selesai, 0, 5);
            
            // Prediksi pertemuan ke- berikutnya secara otomatis
            $lastJurnal = JurnalGuru::where('kelas_id', $this->kelas_id)
                ->where('mata_pelajaran_id', $this->mata_pelajaran_id)
                ->orderBy('pertemuan_ke', 'desc')
                ->first();

            $this->pertemuan_ke = $lastJurnal ? ($lastJurnal->pertemuan_ke + 1) : 1;
        }
    }

    private function resetFormLaporan(): void
    {
        $user = auth()->user();
        $guru = $user ? Guru::where('user_id', $user->id)->first() : null;

        // Jika dia bukan guru, kosongkan field guru_id agar bisa pilih manual
        if (!$guru) {
            $this->guru_id = null;
        } else {
            $this->guru_id = $guru->id;
        }
        
        $this->kelas_id          = null;
        $this->mata_pelajaran_id = null;
        $this->pertemuan_ke      = 1;
        $this->jam_mulai         = '';
        $this->jam_selesai       = '';
        $this->materi            = '';
        $this->kompetensi_dasar  = '';
        $this->deskripsi_kegiatan = '';
        $this->metode_pembelajaran = '';
        $this->media_pembelajaran = '';
        $this->jumlah_hadir      = 0;
        $this->jumlah_tidak_hadir = 0;
        $this->capaian           = 'tercapai';
        $this->tindak_lanjut     = '';
        $this->catatan           = '';
        $this->jurnal_id_tersimpan = null;
        $this->reset('lampiran_file');
    }

    public function simpanJurnal(): void
    {
        $this->validate([
            'tanggal'             => 'required|date',
            'guru_id'             => 'required|integer',
            'kelas_id'            => 'required|integer',
            'mata_pelajaran_id'   => 'required|integer',
            'tahun_ajaran_id'     => 'required|integer',
            'jam_mulai'           => 'required',
            'jam_selesai'         => 'required',
            'pertemuan_ke'        => 'required|integer|min:1',
            'materi'              => 'required|string|max:255',
            'kompetensi_dasar'    => 'required|string',
            'deskripsi_kegiatan'  => 'required|string',
            'metode_pembelajaran' => 'required|string',
            'media_pembelajaran'  => 'nullable|string|max:255',
            'jumlah_hadir'        => 'required|integer|min:0',
            'jumlah_tidak_hadir'  => 'required|integer|min:0',
            'capaian'             => 'required|string',
            'tindak_lanjut'       => 'nullable|string',
            'catatan'             => 'nullable|string',
            'lampiran_file'       => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,xlsm,ppt,pptx|max:10240',
        ]);

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
            'media_pembelajaran'  => $this->media_pembelajaran,
            'jumlah_hadir'        => $this->jumlah_hadir,
            'jumlah_tidak_hadir'  => $this->jumlah_tidak_hadir,
            'capaian'             => $this->capaian,
            'tindak_lanjut'       => $this->tindak_lanjut,
            'catatan'             => $this->catatan,
            'status'              => 'submitted',
            'submitted_at'        => now(),
        ]);

        $this->jurnal_id_tersimpan = $jurnal->id;

        // PROSES UPLOAD DOKUMEN/LAMPIRAN JIKA ADA FILE YANG DIPIILIH
        if ($this->lampiran_file) {
            $file      = $this->lampiran_file;
            $ekstensi  = $file->getClientOriginalExtension();
            $namaFile  = $file->getClientOriginalName();
            $path      = $file->store('jurnal-lampirans/' . $jurnal->id, 'public');

            JurnalLampiran::create([
                'jurnal_guru_id' => $jurnal->id,
                'nama_file'      => $namaFile,
                'path'           => $path,
                'tipe'           => JurnalLampiran::deteksiTipe($ekstensi),
                'ukuran'         => $file->getSize(),
            ]);

            $this->reset('lampiran_file');
        }

        Notification::make()
            ->title('Jurnal Pembelajaran berhasil disimpan!')
            ->success()
            ->send();
    }

    // Fungsi pembantu jika user ingin menambah lampiran lagi setelah form utama disubmit
    public function simpanLampiran(): void
    {
        if (!$this->jurnal_id_tersimpan){
            Notification::make()
            ->title('Sesi tidak valid')
            ->body('Simpan jurnal anda terlebi dahulu sebelum menambahkan lampiran.')
            ->warning()
            ->send();
            return;
        }
        if (!$this->lampiran_file){
            Notification::make()
            ->title('Pilih file terlebih dahulu')
            ->warning()
            ->send();
            return;
        }
        $jurnal = JurnalGuru::find($this->jurnal_id_tersimpan);
        if (!$jurnal){
            Notification::make()
            ->title('Jurnal tidak ditemukan')
            ->danger()
            ->send();
            return;
        }
        try{
            $this->validate([
                'lampiran_file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,xlsm,ppt,pptx|max:10240',
            ]);
        }catch(\Illuminate\Validation\ValidationException $e){
            $pesanError = collect($e->errors())->flatten()->first();
            Notification::make()
            ->title('File tidak valid')
            ->body($pesanError)
            ->warning()
            ->send();
            return;
        }
        $file = $this->lampiran_file;
        $ekkstensi = $file->getClientOriginalExtension();
        $namaFile = $file->getClientOriginalName();
        $ukuran = $file->getSize();
        $path = $file->store('jurnal-lampirans/' . $jurnal->id, 'public');

        if(!$path){
            Notification::make()
            ->title('Gagal mengunggah lampiran')
            ->body('Periksan permission folder storage/app/public')
            ->danger()
            ->send();
            return;
        }

        JurnalLampiran::create([
            'jurnal_guru_id' => $jurnal->id,
            'nama_file'      => $namaFile,
            'path'           => $path,
            'tipe'           => JurnalLampiran::deteksiTipe($ekkstensi),
            'ukuran'         => $ukuran,
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

        Storage::disk('public')->delete($lampiran->path);
        $lampiran->delete();

        Notification::make()
            ->title('Lampiran dihapus')
            ->success()
            ->send();
    }

    public function selesaiInputJurnal(): void
    {   
        if ($this->lampiran_file){
            $this->simpanLampiran();
        }
        $this->resetFormLaporan();
        $this->selected_jadwal_id = null;
        $this->mode_manual = false;
    }
}
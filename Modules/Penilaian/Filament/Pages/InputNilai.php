<?php

namespace Modules\Penilaian\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Livewire\Attributes\Computed;
use Modules\Penilaian\Models\PenilaianItem;
use Modules\Penilaian\Models\PenilaianNilai;
use Modules\Penilaian\Models\PenilaianRekap;
use Modules\Kelas\Models\Kelas;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Carbon\Carbon;

class InputNilai extends Page
{
    protected string $view = 'penilaian::filament.pages.input-nilai';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;
    protected static ?string $navigationLabel = 'Input Nilai';
    protected static string|UnitEnum|null $navigationGroup = 'Penilaian';
    protected static ?int $navigationSort = 1;

    // Filter state
    public ?int    $kelas_id          = null;
    public ?int    $mata_pelajaran_id = null;
    public ?int    $tahun_ajaran_id   = null;
    public string  $semester          = '1';
    public string  $jenis_tab         = 'harian';

    // Active item
    public ?int    $active_item_id    = null;

    // Form tambah item baru
    public bool    $show_form_item    = false;
    public string  $form_judul        = '';
    public string  $form_tanggal      = '';

    // Nilai input: [siswa_id => ['nilai' => ..., 'catatan' => ...]]
    public array   $nilaiInput        = [];

    public function mount(): void
    {
        $this->tahun_ajaran_id = TahunAjaran::where('is_aktif', true)->first()?->id;
        $this->form_tanggal    = today()->format('Y-m-d');
    }

    // ── Computed: data master ─────────────────────────────────────────────────

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

    #[Computed]
    public function tahunAjaranList(): array
    {
        return TahunAjaran::orderByDesc('tahun_ajaran')->pluck('tahun_ajaran', 'id')->toArray();
    }

    // ── Computed: siswa di kelas via kelas_pivot ──────────────────────────────

    #[Computed]
    public function siswaList(): \Illuminate\Support\Collection
    {
        if (!$this->kelas_id || !$this->tahun_ajaran_id) return collect();

        return DB::table('siswas')
            ->join('kelas_pivot', 'siswas.id', '=', 'kelas_pivot.siswa_id')
            ->where('kelas_pivot.kelas_id', $this->kelas_id)
            ->where('kelas_pivot.tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('kelas_pivot.is_aktif', true)
            ->whereNull('kelas_pivot.deleted_at')
            ->whereNull('siswas.deleted_at')
            ->orderBy('siswas.nama_lengkap')
            ->select('siswas.id', 'siswas.nama_lengkap', 'siswas.nis')
            ->get();
    }

    // ── Computed: daftar item grouped by jenis ────────────────────────────────

    #[Computed]
    public function itemList(): array
    {
        if (!$this->kelas_id || !$this->mata_pelajaran_id || !$this->tahun_ajaran_id) {
            return [];
        }

        $items = PenilaianItem::where('kelas_id', $this->kelas_id)
            ->where('mata_pelajaran_id', $this->mata_pelajaran_id)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('semester', $this->semester)
            ->whereNull('deleted_at')
            ->orderBy('tanggal')
            ->get();

        $grouped = [];
        foreach (['harian', 'tugas', 'pts', 'pas'] as $jenis) {
            $grouped[$jenis] = $items->where('jenis', $jenis)->values();
        }
        return $grouped;
    }

    // ── Computed: nilai untuk item aktif ─────────────────────────────────────

    #[Computed]
    public function nilaiForItem(): array
    {
        if (!$this->active_item_id) return [];

        return PenilaianNilai::where('item_id', $this->active_item_id)
            ->get()
            ->keyBy('siswa_id')
            ->toArray();
    }

    // ── Computed: statistik item aktif ───────────────────────────────────────

    #[Computed]
    public function rekapItem(): array
    {
        if (!$this->active_item_id) return [];

        $nilais = PenilaianNilai::where('item_id', $this->active_item_id)
            ->whereNotNull('nilai')
            ->pluck('nilai');

        if ($nilais->isEmpty()) return ['rata' => 0, 'min' => 0, 'max' => 0, 'count' => 0];

        return [
            'rata'  => round($nilais->avg(), 1),
            'min'   => $nilais->min(),
            'max'   => $nilais->max(),
            'count' => $nilais->count(),
        ];
    }

    #[Computed]
    public function activeItem(): ?PenilaianItem
    {
        if (!$this->active_item_id) return null;
        return PenilaianItem::find($this->active_item_id);
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function updatedKelasId(): void
    {
        $this->reset(['active_item_id', 'nilaiInput', 'show_form_item']);
        unset($this->siswaList, $this->itemList);
    }

    public function updatedMataPelajaranId(): void
    {
        $this->reset(['active_item_id', 'nilaiInput', 'show_form_item']);
        unset($this->itemList);
    }

    public function updatedSemester(): void
    {
        $this->reset(['active_item_id', 'nilaiInput', 'show_form_item']);
        unset($this->itemList);
    }

    public function updatedJenisTab(): void
    {
        $this->reset(['active_item_id', 'nilaiInput']);
    }

    public function pilihItem(int $itemId): void
    {
        $this->active_item_id = $itemId;
        $this->show_form_item = false;

        // Pre-fill nilaiInput dari DB
        $existing = PenilaianNilai::where('item_id', $itemId)->get()->keyBy('siswa_id');
        $this->nilaiInput = [];

        foreach ($this->siswaList as $siswa) {
            $entry = $existing->get($siswa->id);
            $this->nilaiInput[$siswa->id] = [
                'nilai'   => $entry?->nilai ?? '',
                'catatan' => $entry?->catatan ?? '',
            ];
        }

        unset($this->nilaiForItem, $this->rekapItem, $this->activeItem);
    }

    public function tambahItem(): void
    {
        $this->validate([
            'form_judul'   => 'required|string|max:255',
            'form_tanggal' => 'required|date',
        ], [
            'form_judul.required'   => 'Judul wajib diisi.',
            'form_tanggal.required' => 'Tanggal wajib diisi.',
        ]);

        if (!$this->kelas_id || !$this->mata_pelajaran_id || !$this->tahun_ajaran_id) {
            Notification::make()->title('Lengkapi filter terlebih dahulu')->warning()->send();
            return;
        }

        $item = PenilaianItem::create([
            'kelas_id'          => $this->kelas_id,
            'mata_pelajaran_id'  => $this->mata_pelajaran_id,
            'guru_id'            => auth()->user()->guru?->id,
            'tahun_ajaran_id'    => $this->tahun_ajaran_id,
            'semester'           => $this->semester,
            'jenis'              => $this->jenis_tab,
            'judul'              => $this->form_judul,
            'tanggal'            => $this->form_tanggal,
        ]);

        $this->reset(['form_judul', 'show_form_item']);
        $this->form_tanggal = today()->format('Y-m-d');

        unset($this->itemList);

        Notification::make()->title('Item penilaian berhasil ditambahkan')->success()->send();

        $this->pilihItem($item->id);
    }

    public function simpanSemua(): void
    {
        if (!$this->active_item_id) return;

        $item    = PenilaianItem::find($this->active_item_id);
        $disimpan = 0;

        foreach ($this->nilaiInput as $siswaId => $data) {
            $nilai = $data['nilai'] !== '' && $data['nilai'] !== null
                ? (float) $data['nilai']
                : null;

            // Validasi range
            if ($nilai !== null && ($nilai < 0 || $nilai > 100)) {
                Notification::make()
                    ->title("Nilai harus antara 0 dan 100")
                    ->warning()
                    ->send();
                return;
            }

            PenilaianNilai::updateOrCreate(
                ['item_id' => $this->active_item_id, 'siswa_id' => $siswaId],
                ['nilai' => $nilai, 'catatan' => $data['catatan'] ?: null]
            );

            // Trigger kalkulasi rekap jika nilai ada
            if ($nilai !== null && $item) {
                PenilaianRekap::kalkulasiDanSimpan(
                    $siswaId,
                    $item->kelas_id,
                    $item->mata_pelajaran_id,
                    $item->tahun_ajaran_id,
                    (int) $item->semester
                );
            }

            $disimpan++;
        }

        unset($this->nilaiForItem, $this->rekapItem);

        Notification::make()
            ->title("Nilai berhasil disimpan ({$disimpan} siswa)")
            ->success()
            ->send();
    }

    public function hapusItem(int $itemId): void
    {
        $item = PenilaianItem::find($itemId);
        if (!$item) return;

        // Soft delete nilai-nilainya dulu
        PenilaianNilai::where('item_id', $itemId)->delete();
        $item->delete();

        if ($this->active_item_id === $itemId) {
            $this->reset(['active_item_id', 'nilaiInput']);
        }

        unset($this->itemList);

        Notification::make()->title('Item penilaian dihapus')->success()->send();
    }
}
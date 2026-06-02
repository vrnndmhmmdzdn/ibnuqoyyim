<?php

namespace Modules\JadwalPelajaran\Filament\Pages;

use Livewire\Component;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Modules\Kelas\Models\Kelas;
use Modules\Guru\Models\Guru;
use Modules\MataPelajaran\Models\MataPelajaran;
use Modules\TahunAjaran\Models\TahunAjaran;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class JadwalPelajaranForm extends Component
{
    // Step 1: Pilih Hari (via tanggal, lalu di-extract nama harinya)
    public $jadwal_date;

    // Step 2: Pilih Durasi (dalam jam)
    public $duration;

    // Step 3: Pilih Kelas & Slot Waktu
    public $selected_kelas;       // kelas_id
    public $selected_time_slot;   // ['start' => '07:30', 'end' => '08:30']

    // Step 4: Detail Jadwal
    public $mata_pelajaran_id;
    public $guru_id;
    public $tahun_ajaran_id;

    // Available slots per kelas
    public $available_slots = [];

    // Jam operasional
    const OPENING_HOUR = 7;
    const CLOSING_HOUR = 15;

    protected $rules = [
        'jadwal_date'       => 'required|date',
        'duration'          => 'required|integer|min:1|max:5',
        'selected_kelas'    => 'required|exists:kelas,id',
        'selected_time_slot'=> 'required|array',
        'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
        'guru_id'           => 'required|exists:gurus,id',
        'tahun_ajaran_id'   => 'required|exists:tahun_ajarans,id',
    ];

    public function mount()
    {
        $this->jadwal_date = Carbon::today()->format('Y-m-d');
    }

    // Livewire naming: updated + StudlyCase dari nama property
    public function updatedJadwalDate()
    {
        $this->reset(['duration', 'selected_kelas', 'selected_time_slot']);
        $this->available_slots = [];
    }

    public function updatedDuration()
    {
        $this->reset(['selected_kelas', 'selected_time_slot']);
        $this->checkAvailableSlots();
    }

    public function checkAvailableSlots()
    {
        if (!$this->jadwal_date || !$this->duration) {
            $this->available_slots = [];
            return;
        }

        $duration   = (int) $this->duration;
        $hariDipilih = Carbon::parse($this->jadwal_date)->locale('id')->isoFormat('dddd');
        // Normalisasi ke format HARI di model (Senin, Selasa, dst.)
        $hariDipilih = ucfirst(strtolower($hariDipilih));

        // Generate semua slot waktu yang mungkin
        $allSlots = [];
        for ($hour = self::OPENING_HOUR; $hour <= self::CLOSING_HOUR - $duration; $hour++) {
            $startTime = sprintf('%02d:00', $hour);
            $endTime   = sprintf('%02d:00', $hour + $duration);
            $allSlots[] = [
                'start' => $startTime,
                'end'   => $endTime,
                'label' => "{$startTime} - {$endTime}",
            ];
        }

        $this->available_slots = [];

        foreach (Kelas::all() as $kelas) {
            $kelasSlots = [];

            foreach ($allSlots as $slot) {
                // Cek apakah slot ini sudah terpakai di kelas + hari yang sama
                $isBooked = JadwalPelajaran::where('kelas_id', $kelas->id)
                    ->where('hari', $hariDipilih)
                    ->where(function ($query) use ($slot) {
                        $query->where(function ($q) use ($slot) {
                            // Slot baru mulai di dalam jadwal existing
                            $q->where('jam_mulai', '<', $slot['end'])
                              ->where('jam_selesai', '>', $slot['start']);
                        });
                    })
                    ->exists();

                if (!$isBooked) {
                    $kelasSlots[] = $slot;
                }
            }

            if (count($kelasSlots) > 0) {
                $this->available_slots[$kelas->id] = [
                    'label' => $kelas->nama_kelas,
                    'slots' => $kelasSlots,
                ];
            }
        }
    }

    public function selectSlot($kelasId, $startTime, $endTime)
    {
        $this->selected_kelas     = $kelasId;
        $this->selected_time_slot = [
            'start' => $startTime,
            'end'   => $endTime,
        ];
    }

    public function createJadwalPelajaran()
    {
        $this->validate();

        $hariDipilih = Carbon::parse($this->jadwal_date)->locale('id')->isoFormat('dddd');
        $hariDipilih = ucfirst(strtolower($hariDipilih));

        // Double check availability sebelum simpan
        $overlapping = JadwalPelajaran::where('kelas_id', $this->selected_kelas)
            ->where('hari', $hariDipilih)
            ->where(function ($query) {
                $query->where('jam_mulai', '<', $this->selected_time_slot['end'])
                      ->where('jam_selesai', '>', $this->selected_time_slot['start']);
            })
            ->exists();

        if ($overlapping) {
            Notification::make()
                ->title('❌ Jadwal Gagal Disimpan')
                ->body('Slot waktu ini sudah terisi di kelas tersebut. Silakan pilih slot lain.')
                ->danger()
                ->send();

            $this->checkAvailableSlots();
            return;
        }

        $mataPelajaran = MataPelajaran::find($this->mata_pelajaran_id);
        $guru          = Guru::find($this->guru_id);

        JadwalPelajaran::create([
            'kelas_id'          => $this->selected_kelas,
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            'guru_id'           => $this->guru_id,
            'tahun_ajaran_id'   => $this->tahun_ajaran_id,
            'hari'              => $hariDipilih,
            'jam_mulai'         => $this->selected_time_slot['start'],
            'jam_selesai'       => $this->selected_time_slot['end'],
        ]);

        Notification::make()
            ->title('✅ Jadwal Berhasil Disimpan!')
            ->body("Jadwal {$mataPelajaran->pelajaran} bersama {$guru->name} berhasil ditambahkan.")
            ->success()
            ->duration(5000)
            ->send();

        $this->reset([
            'duration', 'selected_kelas', 'selected_time_slot',
            'mata_pelajaran_id', 'guru_id', 'tahun_ajaran_id',
        ]);
        $this->available_slots = [];

        return redirect()->route('filament.admin.pages.jadwal-management');
    }

    public function render()
    {
        return view('jadwal-pelajaran::filament.pages.jadwal-pelajaran-form', [
            'listMataPelajaran' => MataPelajaran::orderBy('pelajaran')->get(),
            'listGuru'          => Guru::orderBy('name')->get(),
            'listTahunAjaran'   => TahunAjaran::orderBy('tahun_ajaran', 'desc')->get(),
        ]);
    }
}
<?php

namespace Modules\JadwalPelajaran\Filament\Pages;

use Livewire\Component;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class JadwalPelajaranForm extends Component
{
    // Step 1: Pilih Tanggal
    public $jadwal_date;
    
    // Step 2: Pilih Durasi
    public $duration;
    
    // Step 3: Pilih Slot & Kelas
    public $selected_kelas;
    public $selected_time_slot;
    
    // Data Customer
    public $pelajaran;
    public $hari;
    public $guru;
    public $tahunajaran;
    
    // Available slots
    public $available_slots = [];
    
    // Jam operasional
    const OPENING_HOUR = 8;
    const CLOSING_HOUR = 15;
    
    protected $rules = [
        'jadwal_date' => 'required|date|after_or_equal:today',
        'duration' => 'required|integer|min:1|max:5',
        'selected_kelas' => 'required|string',
        'selected_time_slot' => 'required',
        'pelajaran' => 'required|string|max:255',
        'hari' => 'required|string|max:20',
        'tahunajaran' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->jadwal_date = Carbon::today()->format('Y-m-d');
        $this->checkAvailableSlots();
    }

    public function updatedJadwalPelajaranDate()
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

        $date = Carbon::parse($this->jadwal_date);
        $duration = (int) $this->duration;
        $now = Carbon::now();
        $isToday = $date->isToday();
        
        // Generate all possible time slots
        $allSlots = [];
        for ($hour = self::OPENING_HOUR; $hour <= self::CLOSING_HOUR - $duration; $hour++) {
            $startTime = sprintf('%02d:00', $hour);
            $endTime = sprintf('%02d:00', $hour + $duration);
            
            // Jika jadwal hari ini, skip waktu yang sudah lewat
            if ($isToday) {
                $slotStartDateTime = Carbon::parse($this->jadwal_date . ' ' . $startTime);
                
                // Tambahkan buffer 30 menit dari waktu sekarang
                // Jadi customer harus jadwal minimal 30 menit dari sekarang
                if ($slotStartDateTime->lte($now->copy()->addMinutes(30))) {
                    continue;
                }
            }
            
            $allSlots[] = [
                'start' => $startTime,
                'end' => $endTime,
                'label' => "{$startTime} - {$endTime}"
            ];
        }

        // Check availability for each court and time slot
        $this->available_slots = [];
        
        foreach (JadwalPelajaran::HARI as $courtKey => $courtLabel) {
            $courtSlots = [];
            
            foreach ($allSlots as $slot) {
                $startDateTime = Carbon::parse($this->jadwal_date . ' ' . $slot['start']);
                $endDateTime = Carbon::parse($this->jadwal_date . ' ' . $slot['end']);
                
                // Check if this slot is available
                $isBooked = JadwalPelajaran::where('jadwal_kelas', $courtKey)
                    ->where(function($query) use ($startDateTime, $endDateTime) {
                        $query->whereBetween('jam_mulai', [$startDateTime, $endDateTime->subSecond()])
                              ->orWhereBetween('jam_selesai', [$startDateTime->addSecond(), $endDateTime])
                              ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                                  $q->where('jam_mulai', '<=', $startDateTime)
                                    ->where('jam_selesai', '>=', $endDateTime);
                              });
                    })->exists();
                
                if (!$isBooked) {
                    $courtSlots[] = $slot;
                }
            }
            
            if (count($courtSlots) > 0) {
                $this->available_slots[$courtKey] = [
                    'label' => $courtLabel,
                    'slots' => $courtSlots
                ];
            }
        }
    }

    public function selectSlot($court, $startTime, $endTime)
    {
        $this->selected_kelas = $court;
        $this->selected_time_slot = [
            'start' => $startTime,
            'end' => $endTime
        ];
    }

    public function createJadwalPelajaran()
    {
        $this->validate();

        $startDateTime = Carbon::parse($this->jadwal_date . ' ' . $this->selected_time_slot['start']);
        $endDateTime = Carbon::parse($this->jadwal_date . ' ' . $this->selected_time_slot['end']);
        $now = Carbon::now();

        // Validasi: Tidak bisa jadwal di waktu yang sudah lewat
        if ($startDateTime->lte($now)) {
            Notification::make()
                ->title('⏰ Waktu Tidak Valid')
                ->body('Tidak dapat membuat jadwal untuk waktu yang sudah lewat. Silakan pilih waktu yang lebih dari sekarang.')
                ->warning()
                ->send();
            
            $this->checkAvailableSlots();
            $this->reset(['selected_kelas', 'selected_time_slot']);
            return;
        }

        // Double check availability
        $overlapping = JadwalPelajaran::where('jadwal_kelas', $this->selected_kelas)
            ->where(function($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('jam_mulai', [$startDateTime, $endDateTime->copy()->subSecond()])
                      ->orWhereBetween('jam_selesai', [$startDateTime->copy()->addSecond(), $endDateTime])
                      ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                          $q->where('jam_mulai', '<=', $startDateTime)
                            ->where('jam_selesai', '>=', $endDateTime);
                      });
            })->exists();

        if ($overlapping) {
            Notification::make()
                ->title('❌ JadwalPelajaran Gagal')
                ->body('Slot waktu ini sudah dijadwal oleh customer lain. Silakan pilih slot lain.')
                ->danger()
                ->send();
            
            $this->checkAvailableSlots();
            return;
        }

        JadwalPelajaran::create([
            'pelajaran' => $this->pelajaran,
            'jadwal_kelas' => $this->selected_kelas,
            'jam_mulai' => $startDateTime,
            'jam_selesai' => $endDateTime,
            'tahunajaran' => $this->tahunajaran,
            'hari' => $this->hari,
            'guru' => $this->guru,
        ]);
        
        $totaltahunajaran = $this->tahunajaran * $this->duration;
        
        Notification::make()
            ->title('✅ JadwalPelajaran Berhasil!')
            ->body("JadwalPelajaran {$this->selected_kelas} berhasil dibuat untuk {$this->pelajaran}. Total: Rp " . number_format($totaltahunajaran, 0, ',', '.'))
            ->success()
            ->duration(5000)
            ->send();
        
        // Reset form
        $this->reset(['duration', 'selected_kelas', 'selected_time_slot', 'pelajaran', 'hari', 'guru']);
        $this->tahunajaran = 150000;
        $this->available_slots = [];
        
        // Redirect to refresh the page
        return redirect()->route('filament.admin.pages.jadwal-management');
    }

    public function render()
    {
        return view('jadwal-pelajaran::filament.pages.jadwal-pelajaran-form');
    }
}
<?php

namespace Modules\KalenderDidik\Filament\Pages;

use Livewire\Component;
use Modules\KalenderDidik\Models\Kaldik;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class KaldikForm extends Component
{
    // Step 1: Pilih Tanggal
    public $jadwal_date;
    
    // Step 2: Pilih Durasi
    public $duration;
    
    // Step 3: Pilih Slot & Lapangan
    public $selected_kelas;
    public $selected_time_slot;
    
    // Data Customer
    public $name;
    public $phone;
    public $notes;
    public $price = 200000;
    
    // Available slots
    public $available_slots = [];
    
    // Jam operasional
    const OPENING_HOUR = 6;
    const CLOSING_HOUR = 23;
    
    protected $rules = [
        'jadwal_date' => 'required|date|after_or_equal:today',
        'duration' => 'required|integer|min:1|max:5',
        'selected_kelas' => 'required|string',
        'selected_time_slot' => 'required',
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->jadwal_date = Carbon::today()->format('Y-m-d');
        $this->checkAvailableSlots();
    }

    public function updatedKaldikDate()
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
            
            // Jika kaldik hari ini, skip waktu yang sudah lewat
            if ($isToday) {
                $slotStartDateTime = Carbon::parse($this->jadwal_date . ' ' . $startTime);
                
                // Tambahkan buffer 30 menit dari waktu sekarang
                // Jadi customer harus kaldik minimal 30 menit dari sekarang
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
        
        foreach (Kaldik::subjectS as $courtKey => $courtLabel) {
            $courtSlots = [];
            
            foreach ($allSlots as $slot) {
                $startDateTime = Carbon::parse($this->jadwal_date . ' ' . $slot['start']);
                $endDateTime = Carbon::parse($this->jadwal_date . ' ' . $slot['end']);
                
                // Check if this slot is available
                $isBooked = Kaldik::where('subject', $courtKey)
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

    public function createKaldik()
    {
        $this->validate();

        $startDateTime = Carbon::parse($this->jadwal_date . ' ' . $this->selected_time_slot['start']);
        $endDateTime = Carbon::parse($this->jadwal_date . ' ' . $this->selected_time_slot['end']);
        $now = Carbon::now();

        // Validasi: Tidak bisa kaldik di waktu yang sudah lewat
        if ($startDateTime->lte($now)) {
            Notification::make()
                ->title('⏰ Waktu Tidak Valid')
                ->body('Tidak dapat membuat kaldik untuk waktu yang sudah lewat. Silakan pilih waktu yang lebih dari sekarang.')
                ->warning()
                ->send();
            
            $this->checkAvailableSlots();
            $this->reset(['selected_kelas', 'selected_time_slot']);
            return;
        }

        // Double check availability
        $overlapping = Kaldik::where('subject', $this->selected_kelas)
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
                ->title('❌ Kaldik Gagal')
                ->body('Slot waktu ini sudah dijadwal oleh customer lain. Silakan pilih slot lain.')
                ->danger()
                ->send();
            
            $this->checkAvailableSlots();
            return;
        }

        Kaldik::create([
            'name' => $this->name,
            'subject' => $this->selected_kelas,
            'jam_mulai' => $startDateTime,
            'jam_selesai' => $endDateTime,
            'price' => $this->price,
            'phone' => $this->phone,
            'notes' => $this->notes,
        ]);
        
        $totalPrice = $this->price * $this->duration;
        
        Notification::make()
            ->title('✅ Kaldik Berhasil!')
            ->body("Kaldik {$this->selected_kelas} berhasil dibuat untuk {$this->name}. Total: Rp " . number_format($totalPrice, 0, ',', '.'))
            ->success()
            ->duration(5000)
            ->send();
        
        // Reset form
        $this->reset(['duration', 'selected_kelas', 'selected_time_slot', 'name', 'phone', 'notes']);
        $this->price = 150000;
        $this->available_slots = [];
        
        // Redirect to refresh the page
        return redirect()->route('filament.admin.pages.kaldik-management');
    }

    public function render()
    {
        return view('kalender-didik::filament.pages.kaldik-form');
    }
}
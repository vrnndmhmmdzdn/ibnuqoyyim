<?php

namespace Modules\KalenderDidik\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Modules\KalenderDidik\Models\Kaldik;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;

class KaldikCalendar extends Page implements HasSchemas
{
    use InteractsWithSchemas;
    
    protected string $view = 'kalender-didik::filament.pages.kaldik-calendar';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;

    protected static ?string $navigationLabel = 'Kalender';

    protected static string | UnitEnum | null $navigationGroup = 'Kalender Pendidikan';

    protected static ?int $navigationSort = 1;

    public $currentDate;
    public $selectedDate;

    const WARNA_KEGIATAN = [
        'Ujian'          => ['bg' => '#ef4444', 'border' => '#dc2626'], // merah
        'Libur'          => ['bg' => '#22c55e', 'border' => '#16a34a'], // hijau
        'Akademik'       => ['bg' => '#3b82f6', 'border' => '#2563eb'], // biru
        'Non-Akademik'   => ['bg' => '#f97316', 'border' => '#ea580c'], // orange
    ];
    
    // Form properties
    public ?array $data = [];

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->selectedDate = Carbon::now()->format('Y-m-d');
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('jadwal_date')
                    ->label('Tanggal Kaldik')
                    ->default($this->selectedDate)
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Tim')
                    ->required()
                    ->maxLength(255),
                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required(),
                TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->after('start_time'),
            ])
            ->statePath('data');
    }

    public function createKaldik(array $data): void
    {
        $startDateTime = Carbon::parse($data['created_at'] . ' ' . $data['start_time']);
        $endDateTime = Carbon::parse($data['created_at'] . ' ' . $data['end_time']);

        // Check for overlapping kaldiks
        $overlapping = Kaldik::where(function($query) use ($startDateTime, $endDateTime) {
            $query->whereBetween('jam_mulai', [$startDateTime, $endDateTime])
                  ->orWhereBetween('jam_selesai', [$startDateTime, $endDateTime])
                  ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                      $q->where('jam_mulai', '<=', $startDateTime)
                        ->where('jam_selesai', '>=', $endDateTime);
                  });
        })->exists();

        if ($overlapping) {
            Notification::make()
                ->title('Gagal Membuat Kaldik')
                ->body('Waktu yang dipilih sudah ada kaldik lain.')
                ->danger()
                ->send();
            return;
        }

        Kaldik::create([
            'nama_acara' => $data['nama_acara'],
            'jam_mulai' => $startDateTime,
            'jam_selesai' => $endDateTime,
        ]);
        
        Notification::make()
            ->title('Berhasil ik')
            ->body('Kegiatan pada kaldik berhasil dibuat!')
            ->success()
            ->send();
        
        // Dispatch event untuk refresh calendar
        $this->dispatch('kaldik-created');
    }

    public function previousMonth()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->subMonth();
    }

    public function nextMonth()
    {
        $this->currentDate = Carbon::parse($this->currentDate)->addMonth();
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }

    public function getCurrentDate()
    {
        return $this->currentDate;
    }

    public function getSelectedDate()
    {
        return $this->selectedDate;
    }

    // Computed property untuk mendapatkan semua kaldiks
    public function getKaldiksProperty() : array
    {
        return Kaldik::orderBy('jam_mulai')
            ->get()
            ->map(function (Kaldik $kaldik) {
                $warna = self::WARNA_KEGIATAN[$kaldik->kategori]
                    ?? ['bg' => '#6b7280', 'border' => '#4b5563'];

                return [
                    'id'    => $kaldik->id,
                    'title' => $kaldik->nama_acara,
                    'start' => $kaldik->jam_mulai->format('Y-m-d\TH:i:s'),
                    'end'   => $kaldik->jam_selesai->format('Y-m-d\TH:i:s'),
                    'backgroundColor' => $warna['bg'],
                    'borderColor'     => $warna['border'],
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'nama_acara'   => $kaldik->nama_acara,
                        'kegiatan'     => $kaldik->kegiatan,
                        'kategori'     => $kaldik->kategori,   
                        'subject'      => $kaldik->subject,
                        'tahun_ajaran' => $kaldik->tahun_ajaran,
                        'notes'        => $kaldik->notes ?? '-',
                        'tanggal_mulai'   => $kaldik->jam_mulai->locale('id')->translatedFormat('l, d F Y H:i'),
                        'tanggal_selesai' => $kaldik->jam_selesai->locale('id')->translatedFormat('l, d F Y H:i'),
                    ],
                ];
            })
            ->toArray();
    }

    // Method untuk mendapatkan kaldiks dalam format JSON
    public function getKaldiksJson()
    {
        return json_encode($this->kaldiks);
    }
}
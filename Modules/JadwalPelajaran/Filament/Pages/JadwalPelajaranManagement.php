<?php

namespace Modules\JadwalPelajaran\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class JadwalPelajaranManagement extends Page
{
    protected string $view = 'jadwal-pelajaran::filament.pages.jadwal-pelajaran-management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentPlus;

    protected static ?string $navigationLabel = 'Input Jadwal';

    protected static string | UnitEnum | null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Jadwal Management';

    protected static ?string $slug = 'jadwal-management';

    public function getTitle(): string
    {
        return 'Jadwal Pelajaran Management';
    }

    public function getHeading(): string
    {
        return 'Jadwal Pelajaran Management';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola jadwal pelajaran';
    }
}
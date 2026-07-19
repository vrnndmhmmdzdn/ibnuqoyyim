<?php

namespace Modules\JurnalGuru\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Modules\JurnalGuru\Models\JurnalGuru;
use Modules\Guru\Models\Guru;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use UnitEnum;
use Carbon\Carbon;

class JurnalGuruManagement extends Page
{
    protected string $view = 'jurnal-guru::filament.pages.jurnal-guru-management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartBar;
    protected static ?string $navigationLabel = 'Overview Jurnal';
    protected static string|UnitEnum|null $navigationGroup = 'Jurnal';
    protected static ?int $navigationSort = 3;

    public function getTitle(): string { return 'Manajemen Jurnal Guru'; }
    public function getHeading(): string { return 'Manajemen Jurnal Guru'; }
    public function getSubheading(): ?string { return 'Pantau keaktifan guru dalam mengisi jurnal mengajar'; }
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }
}
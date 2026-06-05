<?php

namespace Modules\MutabaahTahfidz\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Kelas\Models\Kelas;
use Modules\MutabaahTahfidz\Models\MutabaahRecord;
use UnitEnum;

class MutabaahManagement extends Page
{
    protected string $view = 'mutabaah-tahfidz::filament.pages.mutabaah-management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static ?string $navigationLabel  = 'Manajemen Tahfidz';
    protected static string|UnitEnum|null $navigationGroup = 'Mutabaah Tahfidz';
    protected static ?int $navigationSort      = 1;

    public function getTitle(): string    { return 'Manajemen Mutabaah Tahfidz'; }
    public function getHeading(): string  { return 'Manajemen Mutabaah Tahfidz'; }
    public function getSubheading(): ?string { return 'Pantau progress hafalan Al-Qur\'an seluruh kelas'; }
}
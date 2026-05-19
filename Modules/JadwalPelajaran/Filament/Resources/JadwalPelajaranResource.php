<?php

namespace Modules\JadwalPelajaran\Filament\Resources;

use Modules\JadwalPelajaran\Filament\Resources\Pages\CreateJadwalPelajaran;
use Modules\JadwalPelajaran\Filament\Resources\Pages\EditJadwalPelajaran;
use Modules\JadwalPelajaran\Filament\Resources\Pages\ListJadwalPelajarans;
use Modules\JadwalPelajaran\Filament\Resources\Schemas\JadwalPelajaranForm;
use Modules\JadwalPelajaran\Filament\Resources\Tables\JadwalPelajaransTable;
use Modules\JadwalPelajaran\Models\JadwalPelajaran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use UnitEnum;

class JadwalPelajaranResource extends Resource
{
    protected static ?string $model = JadwalPelajaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jadwal';

    protected static ?string $navigationLabel = ' Jadwal Pelajaran';

    protected static string | UnitEnum | null $navigationGroup = 'Akademik';

    public static function form(Schema $schema): Schema
    {
        return JadwalPelajaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalPelajaransTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwalPelajarans::route('/'),
            'create' => CreateJadwalPelajaran::route('/create'),
            'edit' => EditJadwalPelajaran::route('/{record}/edit'),
        ];
    }
}

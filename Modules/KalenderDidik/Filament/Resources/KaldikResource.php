<?php

namespace Modules\KalenderDidik\Filament\Resources;

use Modules\KalenderDidik\Filament\Resources\Pages\CreateKaldik;
use Modules\KalenderDidik\Filament\Resources\Pages\EditKaldik;
use Modules\KalenderDidik\Filament\Resources\Pages\ListKaldiks;
use Modules\KalenderDidik\Filament\Resources\Schemas\KaldikForm;
use Modules\KalenderDidik\Filament\Resources\Tables\KaldikTable;
use Modules\KalenderDidik\Models\Kaldik;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use UnitEnum;

class KaldikResource extends Resource
{
    protected static ?string $model = Kaldik::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'kaldik';

    protected static ?string $navigationLabel = 'List Kaldik';

    protected static string | UnitEnum | null $navigationGroup = 'Kalender Pendidikan';

    public static function form(Schema $schema): Schema
    {
        return KaldikForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KaldikTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKaldiks::route('/'),
            'create' => CreateKaldik::route('/create'),
            'edit' => EditKaldik::route('/{record}/edit'),
        ];
    }
}

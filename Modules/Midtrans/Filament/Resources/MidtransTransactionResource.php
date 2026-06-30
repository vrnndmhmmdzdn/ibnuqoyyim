<?php

namespace Modules\Midtrans\Filament\Resources;

use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Midtrans\Models\MidtransTransaction;
use Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Pages;
use Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Schemas\MidtransTransactionForm;
use Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Tables\MidtransTransactionTable;
use Modules\Midtrans\Filament\Resources\MidtransTransactionResource\Infolists\MidtransTransactionInfolist;
use UnitEnum;

class MidtransTransactionResource extends Resource
{
    protected static ?string $model = MidtransTransaction::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Transactions';

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions';

    protected static string | UnitEnum | null $navigationGroup = 'Midtrans';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MidtransTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MidtransTransactionTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MidtransTransactionInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMidtransTransactions::route('/'),
            'create' => Pages\CreateMidtransTransaction::route('/create'),
            'view' => Pages\ViewMidtransTransaction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return true; // Enable create untuk sample penggunaan
    }

    public static function canEdit($record): bool
    {
        return false; // Transactions read-only
    }

    public static function canDelete($record): bool
    {
        return false; // Transactions tidak bisa dihapus
    }
}

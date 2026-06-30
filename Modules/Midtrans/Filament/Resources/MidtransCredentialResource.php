<?php

namespace Modules\Midtrans\Filament\Resources;

use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Modules\Midtrans\Models\MidtransCredential;
use Modules\Midtrans\Filament\Resources\MidtransCredentialResource\Pages;
use Modules\Midtrans\Filament\Resources\MidtransCredentialResource\Schemas\MidtransCredentialForm;
use Modules\Midtrans\Filament\Resources\MidtransCredentialResource\Tables\MidtransCredentialTable;
use UnitEnum;

class MidtransCredentialResource extends Resource
{
    protected static ?string $model = MidtransCredential::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Midtrans Credentials';

    protected static ?string $modelLabel = 'Midtrans Credential';

    protected static ?string $pluralModelLabel = 'Midtrans Credentials';

    protected static string | UnitEnum | null $navigationGroup = 'Midtrans';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MidtransCredentialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MidtransCredentialTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMidtransCredentials::route('/'),
            'create' => Pages\CreateMidtransCredential::route('/create'),
            'edit' => Pages\EditMidtransCredential::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->with(['transactions']);
    }
}

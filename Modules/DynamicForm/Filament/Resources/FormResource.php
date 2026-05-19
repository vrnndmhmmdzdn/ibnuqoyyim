<?php

namespace Modules\DynamicForm\Filament\Resources;

use Modules\DynamicForm\Filament\Resources\Pages\CreateForm;
use Modules\DynamicForm\Filament\Resources\Pages\EditForm;
use Modules\DynamicForm\Filament\Resources\Pages\ListForms;
use Modules\DynamicForm\Filament\Resources\Schemas\FormForm;
use Modules\DynamicForm\Filament\Resources\Tables\FormsTable;
use Modules\DynamicForm\Models\Form;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static string | UnitEnum | null $navigationGroup = 'Dynamic Form';

    protected static ?string $navigationLabel = 'Forms';

    public static function form(Schema $schema): Schema
    {
        return FormForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \Modules\DynamicForm\Filament\Resources\RelationManagers\FormSubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

<?php

namespace Modules\DynamicForm\Filament\Resources;

use Modules\DynamicForm\Filament\Resources\Pages\ListFormSubmissions;
use Modules\DynamicForm\Filament\Resources\Pages\ViewFormSubmission;
use Modules\DynamicForm\Filament\Resources\Schemas\FormSubmissionForm;
use Modules\DynamicForm\Filament\Resources\Tables\FormSubmissionsTable;
use Modules\DynamicForm\Models\FormSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static string | UnitEnum | null $navigationGroup = 'Dynamic Form';

    protected static ?string $navigationLabel = 'Submissions';

    public static function form(Schema $schema): Schema
    {
        return FormSubmissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormSubmissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormSubmissions::route('/'),
            'view' => ViewFormSubmission::route('/{record}'),
        ];
    }
}


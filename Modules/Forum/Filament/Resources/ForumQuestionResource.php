<?php

namespace Modules\Forum\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Forum\Filament\Resources\ForumQuestionResource\Pages;
use Modules\Forum\Filament\Resources\ForumQuestionResource\RelationManagers\CommentsRelationManager;
use Modules\Forum\Filament\Resources\ForumQuestionResource\Schemas\ForumQuestionForm;
use Modules\Forum\Filament\Resources\ForumQuestionResource\Tables\ForumQuestionsTable;
use Modules\Forum\Models\ForumQuestion;
use UnitEnum;

class ForumQuestionResource extends Resource
{
    protected static ?string $model = ForumQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Pengumuman & Forum';

    protected static ?string $navigationLabel = 'List Pengumuman';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ForumQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumQuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForumQuestions::route('/'),
            'create' => Pages\CreateForumQuestion::route('/create'),
            'edit' => Pages\EditForumQuestion::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->withCount('comments');
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }
}

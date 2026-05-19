<?php

namespace Modules\Forum\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Forum\Filament\Resources\ForumCommentResource\Pages;
use Modules\Forum\Filament\Resources\ForumCommentResource\Schemas\ForumCommentForm;
use Modules\Forum\Filament\Resources\ForumCommentResource\Tables\ForumCommentsTable;
use Modules\Forum\Models\ForumComment;
use UnitEnum;

class ForumCommentResource extends Resource
{
    protected static ?string $model = ForumComment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChatBubbleOvalLeftEllipsis;

    protected static string|UnitEnum|null $navigationGroup = 'Pengumuman & Forum';

    protected static ?string $navigationLabel = 'List Komentar';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ForumCommentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumCommentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListForumComments::route('/'),
            'create' => Pages\CreateForumComment::route('/create'),
            'edit' => Pages\EditForumComment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['question', 'user']);
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

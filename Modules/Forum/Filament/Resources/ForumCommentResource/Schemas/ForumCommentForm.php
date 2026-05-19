<?php

namespace Modules\Forum\Filament\Resources\ForumCommentResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Forum\Models\ForumQuestion;

class ForumCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Comment')
                    ->schema([
                        Forms\Components\Select::make('question_id')
                            ->label('Question')
                            ->required()
                            ->options(fn () => ForumQuestion::query()->orderByDesc('created_at')->pluck('title', 'id'))
                            ->searchable(),
                        Forms\Components\RichEditor::make('body')
                            ->required()
                            ->json(false)
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike'],
                                ['h2', 'h3', 'blockquote', 'codeBlock'],
                                ['bulletList', 'orderedList'],
                                ['link'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->image()
                            ->multiple()
                            ->disk('public')
                            ->directory('forum/comments')
                            ->visibility('public')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}

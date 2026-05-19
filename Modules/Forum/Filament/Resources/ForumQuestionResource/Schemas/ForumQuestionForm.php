<?php

namespace Modules\Forum\Filament\Resources\ForumQuestionResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ForumQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Question')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
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
                            ->directory('forum/questions')
                            ->visibility('public')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}

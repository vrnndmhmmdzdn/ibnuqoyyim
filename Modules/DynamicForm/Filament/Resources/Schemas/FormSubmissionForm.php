<?php

namespace Modules\DynamicForm\Filament\Resources\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Submission Information')
                    ->schema([
                        TextInput::make('form.title')
                            ->label('Form')
                            ->disabled(),
                        TextInput::make('submission_token')
                            ->disabled(),
                        TextInput::make('responder_email')
                            ->disabled(),
                        TextInput::make('responder_name')
                            ->disabled(),
                        TextInput::make('submitted_at')
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Response Data')
                    ->schema([
                        KeyValue::make('data')
                            ->label('Answers')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}


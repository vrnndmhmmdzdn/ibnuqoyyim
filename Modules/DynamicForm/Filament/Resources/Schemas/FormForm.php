<?php

namespace Modules\DynamicForm\Filament\Resources\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, callable $set) {
                                $set('slug', Str::slug($state));
                            }),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->readOnly(),
                        TextInput::make('public_url')
                            ->label('Form URL')
                            ->formatStateUsing(fn ($state, $record) => $record?->public_url)
                            ->readOnly()
                            ->copyable()
                            ->dehydrated(false)
                            ->visible(fn ($record) => filled($record))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Settings')
                    ->schema([
                        Checkbox::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Checkbox::make('require_login')
                            ->label('Require Login')
                            ->default(false),
                        Checkbox::make('allow_multiple_submissions')
                            ->label('Allow Multiple Submissions')
                            ->default(true),
                        Checkbox::make('collect_email')
                            ->label('Collect Email')
                            ->default(false),
                        DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Form Builder')
                    ->schema([
                        Textarea::make('schema_json')
                            ->label('SurveyJS JSON Schema')
                            ->placeholder('Paste your SurveyJS JSON questionnaire here...')
                            ->rows(15)
                            ->hintAction(
                                Action::make('openSurveyBuilder')
                                    ->label('Open SurveyJS Builder')
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->url('https://surveyjs.io/create-free-survey', true)
                            )
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                // Load existing schema as JSON string when editing
                                if ($record && $record->schema) {
                                    $jsonString = json_encode($record->schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    $component->state($jsonString);
                                } else {
                                    // Default empty schema
                                    $component->state(json_encode([
                                        'pages' => [
                                            [
                                                'name' => 'page1',
                                                'elements' => [],
                                            ],
                                        ],
                                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                                }
                            }),
                    ])
                    ->description('This dynamic form builder uses the SurveyJS engine. Create your form at https://surveyjs.io/create-free-survey and paste the JSON here.')
                    ->collapsible(),
            ]);
    }
}

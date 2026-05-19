<?php

namespace Modules\DynamicForm\Filament\Resources\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\DynamicForm\Filament\Resources\FormSubmissionResource;

class FormSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Responses';

    private ?array $choiceMapCache = null;

    public function table(Table $table): Table
    {
        $columns = [
            TextColumn::make('responder_name')
                ->label('Responder')
                ->searchable(),
            TextColumn::make('responder_email')
                ->label('Email')
                ->searchable(),
            TextColumn::make('submitted_at')
                ->label('Submitted At')
                ->dateTime()
                ->sortable(),
        ];

        foreach ($this->getQuestionsForTable() as $question) {
            if ($question['name'] === 'email') {
                continue;
            }
            $columns[] = TextColumn::make("data.{$question['name']}")
                ->label($question['name'])
                ->getStateUsing(fn ($record) => data_get($record->data, $question['name']))
                ->formatStateUsing(fn ($state) => $this->stringifyAnswer($state, $question['name']))
                ->wrap();
        }

        return $table
            ->columns($columns)
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => FormSubmissionResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->select([
                    'dynamic_form_submissions.id',
                    'dynamic_form_submissions.form_id',
                    'dynamic_form_submissions.submission_token',
                    'dynamic_form_submissions.data',
                    'dynamic_form_submissions.responder_email',
                    'dynamic_form_submissions.responder_name',
                    'dynamic_form_submissions.user_id',
                    'dynamic_form_submissions.ip_address',
                    'dynamic_form_submissions.user_agent',
                    'dynamic_form_submissions.metadata',
                    'dynamic_form_submissions.submitted_at',
                    'dynamic_form_submissions.created_at',
                    'dynamic_form_submissions.updated_at',
                ]);

                return $query->reorder()
                    ->orderBy('submitted_at', 'desc')
                    ->orderBy('id', 'asc');
            });
    }

    private function getSchemaQuestions(): array
    {
        $form = $this->getOwnerRecord();
        $schema = is_array($form?->schema) ? $form->schema : [];
        $questions = [];

        foreach ($schema['pages'] ?? [] as $page) {
            $this->extractElements($page['elements'] ?? [], $questions);
        }

        return $questions;
    }

    private function getQuestionsForTable(): array
    {
        $questions = [];

        foreach ($this->getSchemaQuestions() as $question) {
            $questions[$question['name']] = $question;
        }

        foreach ($this->getSubmissionKeys() as $key) {
            if (!isset($questions[$key])) {
                $questions[$key] = [
                    'name' => $key,
                    'title' => $key,
                ];
            }
        }

        return array_values($questions);
    }

    private function getSubmissionKeys(): array
    {
        $form = $this->getOwnerRecord();
        if (!$form) {
            return [];
        }

        $keys = [];
        $form->submissions()
            ->select('data')
            ->get()
            ->each(function ($submission) use (&$keys) {
                foreach (array_keys((array) $submission->data) as $key) {
                    $keys[$key] = true;
                }
            });

        return array_keys($keys);
    }

    private function extractElements(array $elements, array &$questions): void
    {
        foreach ($elements as $element) {
            $name = $element['name'] ?? null;
            if ($name) {
                $questions[] = [
                    'name' => $name,
                    'title' => $element['title'] ?? $name,
                ];
            }

            if (!empty($element['elements']) && is_array($element['elements'])) {
                $this->extractElements($element['elements'], $questions);
            }

            if (!empty($element['templateElements']) && is_array($element['templateElements'])) {
                $this->extractElements($element['templateElements'], $questions);
            }
        }
    }

    private function stringifyAnswer(mixed $state, string $questionName): string
    {
        $choiceMap = $this->getChoiceMap();
        $questionChoices = $choiceMap[$questionName] ?? [];

        if ($questionChoices !== []) {
            if (is_array($state)) {
                $mapped = array_map(
                    fn ($value) => $questionChoices[(string) $value] ?? $value,
                    $state
                );
                return implode(', ', $mapped);
            }

            if ($state !== null) {
                return (string) ($questionChoices[(string) $state] ?? $state);
            }
        }

        if (is_array($state)) {
            return json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_bool($state)) {
            return $state ? 'Yes' : 'No';
        }

        return (string) $state;
    }

    private function getChoiceMap(): array
    {
        if ($this->choiceMapCache !== null) {
            return $this->choiceMapCache;
        }

        $form = $this->getOwnerRecord();
        $schema = is_array($form?->schema) ? $form->schema : [];
        $map = [];

        foreach ($schema['pages'] ?? [] as $page) {
            $this->extractChoiceMap($page['elements'] ?? [], $map);
        }

        $this->choiceMapCache = $map;

        return $map;
    }

    private function extractChoiceMap(array $elements, array &$map): void
    {
        foreach ($elements as $element) {
            $name = $element['name'] ?? null;
            $choices = $element['choices'] ?? null;

            if ($name && is_array($choices)) {
                foreach ($choices as $choice) {
                    if (is_array($choice)) {
                        $value = $choice['value'] ?? $choice['text'] ?? null;
                        $text = $choice['text'] ?? $choice['value'] ?? null;
                    } else {
                        $value = $choice;
                        $text = $choice;
                    }

                    if ($value !== null) {
                        $map[$name][(string) $value] = (string) $text;
                    }
                }
            }

            if (!empty($element['elements']) && is_array($element['elements'])) {
                $this->extractChoiceMap($element['elements'], $map);
            }

            if (!empty($element['templateElements']) && is_array($element['templateElements'])) {
                $this->extractChoiceMap($element['templateElements'], $map);
            }
        }
    }
}

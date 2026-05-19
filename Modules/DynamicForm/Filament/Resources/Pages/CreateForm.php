<?php

namespace Modules\DynamicForm\Filament\Resources\Pages;

use Modules\DynamicForm\Filament\Resources\FormResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get schema_json from form data
        $schemaJson = $data['schema_json'] ?? null;

        // If schema_json is provided, validate and convert to schema
        if ($schemaJson && !empty(trim($schemaJson))) {
            $decoded = json_decode($schemaJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Notification::make()
                    ->title('Invalid JSON')
                    ->body('Please check your JSON format. Error: ' . json_last_error_msg())
                    ->danger()
                    ->send();

                $this->halt();
            }

            if (!isset($decoded['pages']) || !is_array($decoded['pages'])) {
                Notification::make()
                    ->title('Invalid SurveyJS Schema')
                    ->body('JSON must contain a "pages" array. Please check the SurveyJS documentation.')
                    ->warning()
                    ->send();

                $this->halt();
            }

            $data['schema'] = $decoded;
        }

        // Set default schema jika belum ada
        if (empty($data['schema'])) {
            $data['schema'] = [
                'pages' => [
                    [
                        'name' => 'page1',
                        'elements' => [],
                    ],
                ],
            ];
        }

        // Set default public_link jika belum ada
        if (empty($data['public_link'])) {
            $data['public_link'] = Str::uuid()->toString();
        }

        // Set created_by
        $data['created_by'] = auth()->id();

        // Remove schema_json from data as it's not a database field
        unset($data['schema_json']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return FormResource::getUrl('edit', ['record' => $this->record]);
    }
}

<?php

namespace Modules\DynamicForm\Filament\Resources\Pages;

use Modules\DynamicForm\Filament\Resources\FormResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
        } elseif (empty($data['schema'])) {
            // If no schema_json and no existing schema, set default
            $data['schema'] = [
                'pages' => [
                    [
                        'name' => 'page1',
                        'elements' => [],
                    ],
                ],
            ];
        }

        // Remove schema_json from data as it's not a database field
        unset($data['schema_json']);

        return $data;
    }
}


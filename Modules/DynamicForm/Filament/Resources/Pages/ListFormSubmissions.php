<?php

namespace Modules\DynamicForm\Filament\Resources\Pages;

use Modules\DynamicForm\Filament\Resources\FormSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListFormSubmissions extends ListRecords
{
    protected static string $resource = FormSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}


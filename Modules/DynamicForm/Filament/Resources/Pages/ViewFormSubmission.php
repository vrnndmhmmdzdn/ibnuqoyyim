<?php

namespace Modules\DynamicForm\Filament\Resources\Pages;

use Modules\DynamicForm\Filament\Resources\FormSubmissionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewFormSubmission extends ViewRecord
{
    protected static string $resource = FormSubmissionResource::class;

    protected string $view = 'dynamic-form::filament.pages.view-form-submission';
}


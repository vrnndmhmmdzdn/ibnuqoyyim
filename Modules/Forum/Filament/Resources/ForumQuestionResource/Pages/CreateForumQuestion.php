<?php

namespace Modules\Forum\Filament\Resources\ForumQuestionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Forum\Filament\Resources\ForumQuestionResource;

class CreateForumQuestion extends CreateRecord
{
    protected static string $resource = ForumQuestionResource::class;
}

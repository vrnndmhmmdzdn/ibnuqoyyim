<?php

namespace Modules\Forum\Filament\Resources\ForumQuestionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Forum\Filament\Resources\ForumQuestionResource;

class ListForumQuestions extends ListRecords
{
    protected static string $resource = ForumQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

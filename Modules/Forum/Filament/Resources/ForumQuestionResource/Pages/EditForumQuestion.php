<?php

namespace Modules\Forum\Filament\Resources\ForumQuestionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Forum\Filament\Resources\ForumQuestionResource;

class EditForumQuestion extends EditRecord
{
    protected static string $resource = ForumQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

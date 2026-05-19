<?php

namespace Modules\Forum\Filament\Resources\ForumCommentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Forum\Filament\Resources\ForumCommentResource;

class EditForumComment extends EditRecord
{
    protected static string $resource = ForumCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

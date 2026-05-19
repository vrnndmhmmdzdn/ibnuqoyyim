<?php

namespace Modules\Forum\Filament\Resources\ForumCommentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Forum\Filament\Resources\ForumCommentResource;

class ListForumComments extends ListRecords
{
    protected static string $resource = ForumCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

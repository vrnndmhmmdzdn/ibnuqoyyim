<?php

namespace Modules\Forum\Filament\Resources\ForumCommentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Forum\Filament\Resources\ForumCommentResource;

class CreateForumComment extends CreateRecord
{
    protected static string $resource = ForumCommentResource::class;
}

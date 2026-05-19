<?php

namespace Modules\Forum\Policies;

use App\Models\User;
use Modules\Forum\Models\ForumComment;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ForumComment $comment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ForumComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function delete(User $user, ForumComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

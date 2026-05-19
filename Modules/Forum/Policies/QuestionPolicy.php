<?php

namespace Modules\Forum\Policies;

use App\Models\User;
use Modules\Forum\Models\ForumQuestion;

class QuestionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ForumQuestion $question): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ForumQuestion $question): bool
    {
        return $user->id === $question->user_id;
    }

    public function delete(User $user, ForumQuestion $question): bool
    {
        return $user->id === $question->user_id;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}

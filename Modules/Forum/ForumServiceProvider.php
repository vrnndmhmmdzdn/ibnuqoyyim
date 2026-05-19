<?php

namespace Modules\Forum;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Forum\Models\ForumComment;
use Modules\Forum\Models\ForumQuestion;
use Modules\Forum\Policies\CommentPolicy;
use Modules\Forum\Policies\QuestionPolicy;
use Modules\Forum\Livewire\ForumIndex;
use Modules\Forum\Livewire\ForumShow;

class ForumServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'forum');

        $this->registerPolicies();
        $this->registerLivewireComponents();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(ForumQuestion::class, QuestionPolicy::class);
        Gate::policy(ForumComment::class, CommentPolicy::class);
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('forum.index', ForumIndex::class);
        Livewire::component('forum.show', ForumShow::class);
    }
}

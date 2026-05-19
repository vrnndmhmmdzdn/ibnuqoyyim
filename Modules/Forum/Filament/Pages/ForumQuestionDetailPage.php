<?php

namespace Modules\Forum\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;
use Modules\Forum\Models\ForumQuestion;

class ForumQuestionDetailPage extends Page
{
    protected string $view = 'forum::filament.pages.forum-question-detail-page';

    protected Width|string|null $maxWidth = Width::Full;

    protected static ?string $slug = 'forum/questions/{slug}';

    protected static bool $shouldRegisterNavigation = false;

    public string $questionSlug;
    public ?int $fromPage = null;

    public ?ForumQuestion $question = null;

    public function mount(string $slug): void
    {
        $this->questionSlug = $slug;
        $this->fromPage = request()->integer('from_page') ?: null;
        $this->question = ForumQuestion::query()->where('slug', $slug)->first();

        abort_unless($this->question, 404);
    }

    public function getTitle(): string
    {
        return $this->question?->title ?? 'Detail Forum';
    }

    public function getHeading(): string | HtmlString
    {
        $title = e($this->getTitle());
        $backParams = array_filter([
            'focus' => $this->questionSlug,
            'page' => $this->fromPage,
        ], fn ($value) => ! is_null($value) && $value !== '');
        $backUrl = e(ForumQuestionsPage::getUrl($backParams));

        return new HtmlString(
            '<div class="flex flex-wrap items-center gap-3">' .
                '<a href="' . $backUrl . '" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100 dark:border-white/10 dark:text-gray-300 dark:hover:bg-gray-800">' .
                    '<span aria-hidden="true">←</span>' .
                    '<span>Back</span>' .
                '</a>' .
                '<span>' . $title . '</span>' .
            '</div>'
        );
    }
}

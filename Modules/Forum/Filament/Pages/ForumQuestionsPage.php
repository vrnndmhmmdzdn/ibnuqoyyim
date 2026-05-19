<?php

namespace Modules\Forum\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Width;
use UnitEnum;

class ForumQuestionsPage extends Page
{
    protected string $view = 'forum::filament.pages.forum-questions-page';

    protected Width|string|null $maxWidth = Width::Full;

    protected static ?string $title = 'Forum';

    protected static ?string $slug = 'forum';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Pengumuman & Forum';

    protected static ?string $navigationLabel = 'Halaman Pengumuman';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public static function getNavigationItemActiveRoutePattern(): string | array
    {
        return [
            static::getRouteName(),
            ForumQuestionDetailPage::getRouteName(),
        ];
    }
}

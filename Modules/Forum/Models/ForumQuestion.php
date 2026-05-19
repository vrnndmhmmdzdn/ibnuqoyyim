<?php

namespace Modules\Forum\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ForumQuestion extends Model
{
    protected $table = 'forum_questions';

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'body',
        'images',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ForumQuestion $question): void {
            if (empty($question->user_id) && Auth::check()) {
                $question->user_id = Auth::id();
            }

            if (empty($question->slug) && !empty($question->title)) {
                $question->slug = static::generateUniqueSlug($question->title);
            }
        });

        static::updating(function (ForumQuestion $question): void {
            if ($question->isDirty('title')) {
                $question->slug = static::generateUniqueSlug($question->title, $question->id);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'question_id');
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        if (blank($baseSlug)) {
            $baseSlug = 'question';
        }
        $slug = $baseSlug;
        $counter = 1;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

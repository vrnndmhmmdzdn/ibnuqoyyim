<?php

namespace Modules\Forum\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ForumComment extends Model
{
    protected $table = 'forum_comments';

    protected $fillable = [
        'question_id',
        'user_id',
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
        static::creating(function (ForumComment $comment): void {
            if (empty($comment->user_id) && Auth::check()) {
                $comment->user_id = Auth::id();
            }
        });
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ForumQuestion::class, 'question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

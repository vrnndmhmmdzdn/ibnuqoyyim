<?php

namespace Modules\DynamicForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\DynamicForm\Database\Factories\FormFactory;

class Form extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dynamic_forms';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return FormFactory::new();
    }

    protected $fillable = [
        'title',
        'description',
        'slug',
        'public_link',
        'schema',
        'theme',
        'is_active',
        'require_login',
        'allow_multiple_submissions',
        'collect_email',
        'settings',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'schema' => 'array',
        'theme' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'require_login' => 'boolean',
        'allow_multiple_submissions' => 'boolean',
        'collect_email' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($form) {
            if (empty($form->slug)) {
                $form->slug = Str::slug($form->title);
            }
            if (empty($form->public_link)) {
                $form->public_link = Str::uuid()->toString();
            }
        });
    }

    // Relations
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    // Accessors
    public function getPublicUrlAttribute()
    {
        return route('dynamic-form.public', $this->slug);
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->isPast();
    }
}

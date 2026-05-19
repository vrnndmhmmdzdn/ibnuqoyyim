<?php

namespace Modules\DynamicForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\DynamicForm\Database\Factories\FormSubmissionFactory;

class FormSubmission extends Model
{
    use HasFactory;

    protected $table = 'dynamic_form_submissions';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return FormSubmissionFactory::new();
    }

    protected $fillable = [
        'form_id',
        'submission_token',
        'data',
        'responder_email',
        'responder_name',
        'user_id',
        'ip_address',
        'user_agent',
        'metadata',
        'submitted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'submitted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($submission) {
            if (empty($submission->submission_token)) {
                $submission->submission_token = Str::random(32);
            }
            if (empty($submission->submitted_at)) {
                $submission->submitted_at = now();
            }
        });
    }

    // Relations
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

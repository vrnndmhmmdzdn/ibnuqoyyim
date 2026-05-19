<?php

namespace Modules\DynamicForm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\DynamicForm\Models\Form;
use Modules\DynamicForm\Models\FormSubmission;

class FormSubmissionFactory extends Factory
{
    protected $model = FormSubmission::class;

    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'submission_token' => Str::random(32),
            'data' => [
                'question1' => $this->faker->name(),
            ],
            'responder_email' => $this->faker->optional()->email(),
            'responder_name' => $this->faker->optional()->name(),
            'user_id' => null,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'metadata' => null,
            'submitted_at' => now(),
        ];
    }
}


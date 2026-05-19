<?php

namespace Modules\DynamicForm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\DynamicForm\Models\Form;

class FormFactory extends Factory
{
    protected $model = Form::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        
        return [
            'title' => $title,
            'description' => $this->faker->paragraph(),
            'slug' => Str::slug($title),
            'public_link' => Str::uuid()->toString(),
            'schema' => [
                'pages' => [
                    [
                        'name' => 'page1',
                        'elements' => [
                            [
                                'type' => 'text',
                                'name' => 'question1',
                                'title' => 'What is your name?',
                                'isRequired' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'theme' => null,
            'is_active' => true,
            'require_login' => false,
            'allow_multiple_submissions' => true,
            'collect_email' => false,
            'settings' => null,
            'expires_at' => null,
            'created_by' => null,
        ];
    }
}


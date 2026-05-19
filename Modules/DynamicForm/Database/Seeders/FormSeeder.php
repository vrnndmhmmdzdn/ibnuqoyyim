<?php

namespace Modules\DynamicForm\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\DynamicForm\Models\Form;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Form::updateOrCreate(
            ['slug' => 'survey-mini-konsumsi-kopi-kekinian'],
            [
                'title' => 'Survey Mini: Konsumsi Kopi Kekinian',
                'description' => 'Cuma 1 menit. Jawaban kamu bantu banget buat riset kecil-kecilan.',
                'schema' => [
                    'pages' => [
                        [
                            'name' => 'page1',
                            'elements' => [
                                [
                                    'type' => 'radiogroup',
                                    'name' => 'seberapa_sering',
                                    'title' => 'Seberapa sering kamu minum kopi dalam seminggu?',
                                    'choices' => [
                                        ['value' => 'ngga pernah', 'text' => 'Nggak Pernah Minum'],
                                        '< 2 kali',
                                        '2-4 kali',
                                        '5-7 kali',
                                        '> 7 kali',
                                    ],
                                ],
                                [
                                    'type' => 'dropdown',
                                    'name' => 'tipe_kopi',
                                    'title' => 'Menu kopi favorit kamu yang paling sering diminum?',
                                    'choices' => [
                                        ['value' => 'gula aren', 'text' => 'Es kopi susu gula aren'],
                                        ['value' => 'americano', 'text' => 'Americano'],
                                        ['value' => 'latte', 'text' => 'Latte'],
                                        ['value' => 'cappuccino', 'text' => 'Cappuccino'],
                                        ['value' => 'manual brew', 'text' => 'Manual brew'],
                                    ],
                                ],
                                [
                                    'type' => 'checkbox',
                                    'name' => 'coffee_shop_favorit',
                                    'title' => 'Apa coffee shop favorit kamu ? Bisa pilih dari 1',
                                    'choices' => [
                                        ['value' => 'kopi kenangan', 'text' => 'Kopi Kenangan'],
                                        ['value' => 'fore', 'text' => 'Fore'],
                                        ['value' => 'janji jiwa', 'text' => 'Janji Jiwa'],
                                        ['value' => 'point coffee', 'text' => 'Point Coffee'],
                                        ['value' => 'kopi tuku', 'text' => 'Kopi Tuku'],
                                    ],
                                    "showOtherItem" => true
                                ],
                            ],
                        ],
                    ],
                    'headerView' => 'advanced',
                ],
                'theme' => null,
                'is_active' => true,
                'require_login' => false,
                'allow_multiple_submissions' => true,
                'collect_email' => false,
                'settings' => null,
                'expires_at' => null,
                'created_by' => null,
            ]
        );

        Form::updateOrCreate(
            ['slug' => 'developer-profile-survey'],
            [
                'title' => 'Developer Profile Survey',
                'description' => 'Survey singkat untuk memahami latar belakang dan skill developer.',
                'schema' => [
                    'title' => 'Developer Profile Survey',
                    'description' => 'Survey singkat untuk memahami latar belakang dan skill developer.',
                    'logoPosition' => 'right',
                    'pages' => [
                        [
                            'name' => 'page1',
                            'elements' => [
                                [
                                    'type' => 'radiogroup',
                                    'name' => 'experience',
                                    'title' => '1) Berapa lama pengalaman kamu di dunia programming?',
                                    'isRequired' => true,
                                    'choices' => [
                                        ['value' => 'lt1', 'text' => 'Kurang dari 1 tahun'],
                                        ['value' => '1_2', 'text' => '1-2 tahun'],
                                        ['value' => '3_5', 'text' => '3-5 tahun'],
                                        ['value' => 'gt5', 'text' => 'Lebih dari 5 tahun'],
                                    ],
                                ],
                                [
                                    'type' => 'checkbox',
                                    'name' => 'main_stack',
                                    'title' => '2) Teknologi apa saja yang kamu kuasai? (boleh pilih lebih dari satu)',
                                    'isRequired' => true,
                                    'choices' => [
                                        ['value' => 'frontend', 'text' => 'Frontend (HTML, CSS, JS, React, Vue, dll)'],
                                        ['value' => 'backend', 'text' => 'Backend (Laravel, Node.js, Django, dll)'],
                                        ['value' => 'mobile', 'text' => 'Mobile (Android, Flutter, React Native, dll)'],
                                        ['value' => 'devops', 'text' => 'DevOps (Docker, CI/CD, Cloud, dll)'],
                                        ['value' => 'data', 'text' => 'Data / AI (Python, SQL, ML, dll)'],
                                        ['value' => 'game', 'text' => 'Game Development'],
                                    ],
                                    'showOtherItem' => true,
                                    'otherText' => 'Stack lainnya:',
                                ],
                                [
                                    'type' => 'matrix',
                                    'name' => 'skill_level',
                                    'title' => '3) Seberapa kuat skill kamu di area berikut?',
                                    'isRequired' => true,
                                    'columns' => [
                                        ['value' => 1, 'text' => 'Pemula'],
                                        ['value' => 2, 'text' => 'Cukup'],
                                        ['value' => 3, 'text' => 'Mahir'],
                                        ['value' => 4, 'text' => 'Expert'],
                                    ],
                                    'rows' => [
                                        ['value' => 'frontend', 'text' => 'Frontend'],
                                        ['value' => 'backend', 'text' => 'Backend'],
                                        ['value' => 'database', 'text' => 'Database'],
                                        ['value' => 'api', 'text' => 'API / Integration'],
                                        ['value' => 'devops', 'text' => 'DevOps / Deployment'],
                                    ],
                                ],
                                [
                                    'type' => 'dropdown',
                                    'name' => 'current_status',
                                    'title' => '4) Status kamu saat ini?',
                                    'isRequired' => true,
                                    'choices' => [
                                        ['value' => 'employee', 'text' => 'Bekerja di perusahaan'],
                                        ['value' => 'freelance', 'text' => 'Freelancer'],
                                        ['value' => 'business', 'text' => 'Punya bisnis / startup sendiri'],
                                        ['value' => 'student', 'text' => 'Mahasiswa / pelajar'],
                                        ['value' => 'jobseeker', 'text' => 'Lagi cari kerja'],
                                    ],
                                ],
                                [
                                    'type' => 'text',
                                    'name' => 'goal',
                                    'title' => '5) Apa target kamu sebagai developer dalam 1-2 tahun ke depan?',
                                    'placeholder' => 'Contoh: kerja di startup, bikin SaaS, fulltime freelancer, dll',
                                    'isRequired' => false,
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
            ]
        );
    }
}

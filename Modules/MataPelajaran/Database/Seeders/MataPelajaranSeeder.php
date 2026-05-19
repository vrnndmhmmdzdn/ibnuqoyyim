<?php

namespace Modules\MataPelajaran\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MataPelajaran\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Umum
            ['pelajaran' => 'Matematika',       'kategori' => 'Umum'],
            ['pelajaran' => 'Bahasa Indonesia', 'kategori' => 'Umum'],
            ['pelajaran' => 'IPA',              'kategori' => 'Umum'],
            ['pelajaran' => 'IPS',              'kategori' => 'Umum'],
            ['pelajaran' => 'PJOK',             'kategori' => 'Umum'],
            ['pelajaran' => 'SBdP',             'kategori' => 'Umum'],
            ['pelajaran' => 'Bahasa Inggris',   'kategori' => 'Umum'],
            ['pelajaran' => 'PKn',              'kategori' => 'Umum'],

            // Agama
            ['pelajaran' => 'Al-Quran Hadits',  'kategori' => 'Agama'],
            ['pelajaran' => 'Aqidah Akhlak',    'kategori' => 'Agama'],
            ['pelajaran' => 'Fiqih',            'kategori' => 'Agama'],
            ['pelajaran' => 'SKI',              'kategori' => 'Agama'],
            ['pelajaran' => 'Bahasa Arab',      'kategori' => 'Agama'],
            ['pelajaran' => 'Tahfidz',          'kategori' => 'Agama'],
            ['pelajaran' => 'BTQ',              'kategori' => 'Agama'],

            // Ekstrakurikuler
            ['pelajaran' => 'Pramuka',          'kategori' => 'Ekstrakurikuler'],
            ['pelajaran' => 'Jadwal',           'kategori' => 'Ekstrakurikuler'],
            ['pelajaran' => 'Badminton',        'kategori' => 'Ekstrakurikuler'],
            ['pelajaran' => 'Seni Kaligrafi',   'kategori' => 'Ekstrakurikuler'],
            ['pelajaran' => 'Komputer',         'kategori' => 'Ekstrakurikuler'],
            ['pelajaran' => 'Pidato',           'kategori' => 'Ekstrakurikuler'],
        ];

        foreach ($data as $item) {
            MataPelajaran::create(array_merge($item, ['is_aktif' => true]));
        }
    }
}
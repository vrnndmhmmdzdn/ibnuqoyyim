<?php

namespace Modules\Forum\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Forum\Models\ForumComment;
use Modules\Forum\Models\ForumQuestion;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            User::query()->firstOrCreate(
                ['email' => 'agus@example.com'],
                ['name' => 'Agus', 'password' => bcrypt('password')]
            ),
            User::query()->firstOrCreate(
                ['email' => 'budi@example.com'],
                ['name' => 'Budi', 'password' => bcrypt('password')]
            ),
            User::query()->firstOrCreate(
                ['email' => 'citra@example.com'],
                ['name' => 'Citra', 'password' => bcrypt('password')]
            ),
            User::query()->firstOrCreate(
                ['email' => 'dina@example.com'],
                ['name' => 'Dina', 'password' => bcrypt('password')]
            ),
            User::query()->firstOrCreate(
                ['email' => 'eko@example.com'],
                ['name' => 'Eko', 'password' => bcrypt('password')]
            ),
        ];

        ForumComment::query()->delete();
        ForumQuestion::query()->delete();

        $questions = [
            [
                'title' => 'Cara terbaik validasi request di Laravel 12 pakai Form Request?',
                'body' => 'Saya masih bingung kapan sebaiknya pakai validasi langsung di controller dan kapan pakai Form Request. Kalau project mulai besar, pola yang paling rapi bagaimana?',
                'comments' => [
                    'Kalau endpoint lebih dari satu field, langsung pakai Form Request supaya controller tetap tipis.',
                    'Tambahkan method authorize() di Form Request agar sekalian handle izin akses.',
                    'Saya biasanya simpan custom message juga di Form Request biar reusable.',
                ],
            ],
            [
                'title' => 'Perbedaan eager loading with() vs load() di Eloquent itu apa?',
                'body' => 'Kadang lihat kode pakai with(), kadang load(). Saya ingin tahu perbedaan waktu pakai dan dampaknya ke performa query.',
                'comments' => [
                    'with() dipakai saat query awal, load() setelah model sudah didapat.',
                    'Kalau sudah punya collection, load() lebih praktis daripada query ulang.',
                ],
            ],
            [
                'title' => 'Bagaimana cara bikin slug otomatis dan tetap unik di Laravel?',
                'body' => 'Saya ingin slug dari title, tapi kalau title sama jangan bentrok. Idealnya di model atau observer?',
                'comments' => [
                    'Bisa di event creating pada model, cek slug existing lalu tambahkan suffix angka.',
                    'Observer oke juga kalau mau pisahkan logic dari model.',
                    'Pastikan kolom slug diberi unique index di migration.',
                ],
            ],
            [
                'title' => 'Queue Laravel paling cocok untuk kirim email massal?',
                'body' => 'Rencananya kirim email notifikasi ke banyak user. Driver queue apa yang aman untuk production dan monitoringnya pakai apa?',
                'comments' => [
                    'Kalau sederhana bisa database/redis, tapi redis lebih cepat untuk beban besar.',
                    'Pakai Horizon kalau pakai Redis, enak buat monitor failed jobs.',
                    'Jangan lupa retry dan timeout di job biar stabil.',
                ],
            ],
            [
                'title' => 'Cara handle N+1 problem yang sering kejadian di Laravel?',
                'body' => 'Saya sering tidak sadar kena N+1 saat render list + relasi. Tools apa yang kalian pakai untuk deteksi cepat?',
                'comments' => [
                    'Aktifkan Laravel Debugbar saat development, langsung kelihatan query berulang.',
                    'Biasakan with() relasi di query index/list.',
                    'Di Laravel bisa prevent lazy loading saat local supaya cepat ketahuan.',
                ],
            ],
            [
                'title' => 'Lebih baik pakai Repository pattern atau langsung Eloquent?',
                'body' => 'Di tim saya ada perdebatan soal repository pattern. Untuk aplikasi menengah, apakah worth it atau malah overengineering?',
                'comments' => [
                    'Kalau logic query masih sederhana, Eloquent langsung biasanya cukup.',
                    'Repository berguna kalau mau swap data source atau query sangat kompleks.',
                    'Yang penting konsisten standar tim, jangan campur aduk.',
                ],
            ],
            [
                'title' => 'Tips optimasi query pagination di tabel besar Laravel',
                'body' => 'Data saya sudah ratusan ribu baris dan pagination mulai lambat. Strategi optimasi apa yang bisa dipakai?',
                'comments' => [
                    'Pastikan kolom filter/sort punya index yang tepat.',
                    'Untuk data besar bisa pertimbangkan cursorPagination().',
                    'Hindari select * kalau kolomnya banyak, ambil yang diperlukan saja.',
                ],
            ],
            [
                'title' => 'Cara setup Policy agar user hanya bisa edit data miliknya sendiri',
                'body' => 'Saya ingin menerapkan aturan user hanya boleh update/delete record milik sendiri, tapi admin tetap bisa akses semua.',
                'comments' => [
                    'Gunakan method before() di policy untuk bypass admin.',
                    'Di update/delete bandingkan auth user id dengan user_id record.',
                    'Pastikan Filament resource juga membaca policy yang sama.',
                ],
            ],
            [
                'title' => 'Apa praktik terbaik struktur module di Laravel monolith?',
                'body' => 'Saya lagi coba modularisasi folder berdasarkan domain. Ada saran struktur folder yang rapi dan mudah scale?',
                'comments' => [
                    'Pisahkan per domain: Models, Services, Routes, Filament, Migration per module.',
                    'Tambahkan service provider tiap module untuk registrasi route dan migration.',
                    'Gunakan namespace konsisten supaya autoload tidak membingungkan.',
                ],
            ],
            [
                'title' => 'Bagaimana cara testing Feature test untuk endpoint API Laravel?',
                'body' => 'Saya masih baru nulis test. Minimal skenario apa yang wajib dibuat untuk endpoint CRUD API?',
                'comments' => [
                    'Mulai dari test sukses create/list/detail/update/delete.',
                    'Tambahkan test validasi gagal dan unauthorized.',
                    'Gunakan RefreshDatabase agar data test bersih tiap run.',
                ],
            ],
            [
                'title' => 'Perlukah pakai DTO di Laravel untuk layer service?',
                'body' => 'Saya lihat beberapa project pakai DTO untuk input service. Kapan DTO membantu dan kapan cukup array biasa?',
                'comments' => [
                    'DTO membantu kalau payload besar dan dipakai lintas layer.',
                    'Untuk fitur kecil, array + validation kadang sudah cukup.',
                ],
            ],
            [
                'title' => 'Cara aman upload multiple image di Laravel + validasi ukuran',
                'body' => 'Saya mau upload banyak gambar sekaligus. Validasi tipe file dan ukuran paling aman bagaimana?',
                'comments' => [
                    'Validasi mimes, max size, dan image dimensions bila perlu.',
                    'Simpan di disk public/private sesuai kebutuhan akses.',
                    'Jangan lupa sanitasi nama file, biasanya pakai hashName().',
                ],
            ],
            [
                'title' => 'Caching yang efektif untuk halaman list di Laravel',
                'body' => 'Halaman list konten cukup berat. Saya ingin pakai cache, tapi takut data stale. Strategi invalidasinya bagaimana?',
                'comments' => [
                    'Pakai cache key per filter/page kalau datanya bervariasi.',
                    'Invalidate saat create/update/delete lewat event/listener.',
                    'Gunakan TTL pendek dulu sambil ukur dampaknya.',
                ],
            ],
            [
                'title' => 'Pilihan auth untuk SPA: Sanctum atau Passport?',
                'body' => 'Untuk SPA internal perusahaan, lebih praktis pakai Sanctum atau Passport? Kebutuhan saya token API sederhana.',
                'comments' => [
                    'Kalau SPA first-party biasanya Sanctum lebih ringan.',
                    'Passport cocok kalau butuh OAuth2 flow yang lengkap.',
                    'Pastikan atur CORS dan CSRF sesuai mode auth yang dipilih.',
                ],
            ],
            [
                'title' => 'Cara deploy Laravel 12 paling aman untuk minim downtime',
                'body' => 'Ada rekomendasi checklist deploy agar update aplikasi minim downtime dan rollback lebih mudah?',
                'comments' => [
                    'Gunakan zero-downtime deploy (symlink release) kalau memungkinkan.',
                    'Jalankan migration yang backward compatible dulu.',
                    'Siapkan health check dan rollback script sebelum deploy.',
                ],
            ],
        ];

        foreach ($questions as $index => $item) {
            $author = $users[$index % count($users)];

            $question = ForumQuestion::query()->create([
                'user_id' => $author->id,
                'title' => $item['title'],
                'body' => $item['body'],
                'images' => [],
                'created_at' => now()->subDays(15 - $index),
                'updated_at' => now()->subDays(15 - $index),
            ]);

            foreach ($item['comments'] as $commentIndex => $commentText) {
                $commentUser = $users[($index + $commentIndex + 1) % count($users)];

                ForumComment::query()->create([
                    'question_id' => $question->id,
                    'user_id' => $commentUser->id,
                    'body' => $commentText,
                    'created_at' => now()->subDays(15 - $index)->addHours($commentIndex + 1),
                    'updated_at' => now()->subDays(15 - $index)->addHours($commentIndex + 1),
                ]);
            }
        }

        $this->command?->info('ForumSeeder: 15 pertanyaan Laravel + komentar berhasil dibuat.');
    }
}

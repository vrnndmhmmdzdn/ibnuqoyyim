<?php

namespace Modules\TelegramAssistant\Support;

use InvalidArgumentException;
use Modules\JurnalGuru\Services\JurnalReminderService;
use Modules\TelegramAssistant\Models\TelegramUser;

class ToolRegistry
{
    /**
     * Skema tool dalam format OpenAI-compatible function calling.
     * HANYA fungsi yang terdaftar di sini yang bisa dipanggil AI —
     * tidak ada raw query, tidak ada akses model bebas.
     *
     * @return array<int, array<string, mixed>>
     */
    public function definitions(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'cek_jurnal_belum_diisi',
                    'description' => 'Melihat daftar guru yang memiliki jadwal mengajar hari ini tetapi belum mengisi jurnal pembelajaran.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'ringkasan_hafalan_kelas',
                    'description' => 'Melihat ringkasan progres hafalan Quran (mutabaah tahfidz) untuk satu kelas tertentu.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'kelas_id' => [
                                'type' => 'integer',
                                'description' => 'ID kelas yang ingin dilihat ringkasannya',
                            ],
                        ],
                        'required' => ['kelas_id'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function execute(string $toolName, array $arguments, TelegramUser $requester): mixed
    {
        if (! $requester->isAllowedToUse($toolName)) {
            throw new ToolAccessDeniedException("Anda tidak memiliki izin untuk menggunakan '{$toolName}'.");
        }

        return match ($toolName) {
            'cek_jurnal_belum_diisi' => $this->cekJurnalBelumDiisi(),
            'ringkasan_hafalan_kelas' => $this->ringkasanHafalanKelas($arguments),
            default => throw new InvalidArgumentException("Tool tidak dikenal: {$toolName}"),
        };
    }

    protected function cekJurnalBelumDiisi(): array
    {
        $data = app(JurnalReminderService::class)->cariGuruBelumMengisi();

        return $data
            ->map(fn (array $item): array => [
                'guru'        => $item['guru']->name,
                'jumlah_jadwal_belum_diisi' => $item['jadwals']->count(),
                'kelas' => $item['jadwals']
                    ->map(fn ($jadwal): string => $jadwal->kelas?->nama_kelas . ' - ' . $jadwal->mataPelajaran?->pelajaran)
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    protected function ringkasanHafalanKelas(array $arguments): array
    {
        $kelasId = (int) ($arguments['kelas_id'] ?? 0);

        if ($kelasId <= 0) {
            throw new InvalidArgumentException('kelas_id wajib diisi dan berupa angka valid.');
        }

        // NOTE: sesuaikan namespace/method berikut dengan service statistik
        // yang sudah ada di modul MutabaahTahfidz Anda.
        // Contoh: app(\Modules\MutabaahTahfidz\Services\StatistikTahfidzService::class)
        //     ->ringkasanPerKelas($kelasId);

        return [
            'kelas_id' => $kelasId,
            'catatan'  => 'Hubungkan method ini ke service statistik MutabaahTahfidz yang sudah ada.',
        ];
    }
}
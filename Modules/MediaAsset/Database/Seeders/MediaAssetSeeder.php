<?php

namespace Modules\MediaAsset\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\MediaAsset\Models\MediaAsset;
use Modules\MediaAsset\Services\MediaAssetService;
use Modules\MediaAsset\Support\MediaAssetUpload;

class MediaAssetSeeder extends Seeder
{
    /**
     * @var list<array{url: string, filename: string}>
     */
    private const IMAGES = [
        ['url' => 'https://picsum.photos/seed/dewafilament-media-01/1600/1200.jpg', 'filename' => 'picsum-seed-01.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-02/1600/1200.jpg', 'filename' => 'picsum-seed-02.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-03/1600/1200.jpg', 'filename' => 'picsum-seed-03.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-04/1600/1200.jpg', 'filename' => 'picsum-seed-04.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-05/1600/1200.jpg', 'filename' => 'picsum-seed-05.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-06/1600/1200.jpg', 'filename' => 'picsum-seed-06.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-07/1600/1200.jpg', 'filename' => 'picsum-seed-07.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-08/1600/1200.jpg', 'filename' => 'picsum-seed-08.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-09/1600/1200.jpg', 'filename' => 'picsum-seed-09.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-10/1600/1200.jpg', 'filename' => 'picsum-seed-10.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-11/1600/1200.jpg', 'filename' => 'picsum-seed-11.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-12/1600/1200.jpg', 'filename' => 'picsum-seed-12.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-13/1600/1200.jpg', 'filename' => 'picsum-seed-13.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-14/1600/1200.jpg', 'filename' => 'picsum-seed-14.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-15/1600/1200.jpg', 'filename' => 'picsum-seed-15.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-16/1600/1200.jpg', 'filename' => 'picsum-seed-16.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-17/1600/1200.jpg', 'filename' => 'picsum-seed-17.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-18/1600/1200.jpg', 'filename' => 'picsum-seed-18.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-19/1600/1200.jpg', 'filename' => 'picsum-seed-19.jpg'],
        ['url' => 'https://picsum.photos/seed/dewafilament-media-20/1600/1200.jpg', 'filename' => 'picsum-seed-20.jpg'],
    ];

    public function run(): void
    {
        $service = app(MediaAssetService::class);
        $storage = Storage::disk(MediaAssetUpload::DEFAULT_DISK);

        $this->cleanupPreviousSeed();
        $storage->deleteDirectory(MediaAssetUpload::TEMP_DIRECTORY . '/seeders');

        $imported = 0;

        foreach (self::IMAGES as $index => $image) {
            $tempPath = MediaAssetUpload::TEMP_DIRECTORY . '/seeders/' . $image['filename'];

            try {
                $response = Http::timeout(30)
                    ->retry(2, 500)
                    ->accept('image/jpeg')
                    ->get($image['url']);
            } catch (ConnectionException) {
                $this->command?->warn(sprintf('Skip image %d: koneksi ke %s gagal.', $index + 1, $image['url']));
                continue;
            }

            if (! $response->successful()) {
                $this->command?->warn(sprintf('Skip image %d: gagal download dari %s', $index + 1, $image['url']));
                continue;
            }

            $storage->put($tempPath, $response->body());

            try {
                $service->createAsset([
                    MediaAssetUpload::FIELD => $tempPath,
                    MediaAssetUpload::FILE_NAMES_FIELD => $image['filename'],
                    'folder' => null,
                ]);

                $imported++;
            } finally {
                if ($storage->exists($tempPath)) {
                    $storage->delete($tempPath);
                }
            }
        }

        $this->command?->info(sprintf('MediaAssetSeeder selesai. %d image berhasil diimport.', $imported));
    }

    private function cleanupPreviousSeed(): void
    {
        MediaAsset::query()
            ->whereHas('media', fn ($query) => $query->where('file_name', 'like', 'picsum-seed-%'))
            ->get()
            ->each
            ->delete();
    }
}

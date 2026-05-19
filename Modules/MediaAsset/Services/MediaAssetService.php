<?php

namespace Modules\MediaAsset\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Modules\MediaAsset\Models\MediaAsset;
use Modules\MediaAsset\Models\MediaAssetFolder;
use Modules\MediaAsset\Support\MediaAssetUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaAssetService
{
    public function normalizeFolder(?string $folder): ?string
    {
        if (blank($folder)) {
            return null;
        }

        return trim(trim($folder), '/');
    }

    public function guessNameFromPath(?string $path): string
    {
        if (blank($path)) {
            return 'Untitled Image';
        }

        $filename = pathinfo($path, PATHINFO_FILENAME);
        $filename = str_replace(['-', '_'], ' ', $filename);

        return Str::title($filename);
    }

    public function guessNameFromOriginalFilename(?string $filename): string
    {
        if (blank($filename)) {
            return 'Untitled Image';
        }

        $filename = pathinfo((string) $filename, PATHINFO_FILENAME);
        $filename = str_replace(['-', '_'], ' ', $filename);

        return Str::title($filename);
    }

    public function resolveOriginalFilename(array $data): ?string
    {
        $fileNames = $data[MediaAssetUpload::FILE_NAMES_FIELD] ?? null;

        if (is_string($fileNames) && filled($fileNames)) {
            return basename($fileNames);
        }

        if (is_array($fileNames)) {
            $first = reset($fileNames);

            if (is_string($first) && filled($first)) {
                return basename($first);
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    public function resolveUploadedPaths(array $data): array
    {
        $uploaded = $data[MediaAssetUpload::FIELD] ?? null;

        if (is_string($uploaded) && filled($uploaded)) {
            return [$uploaded];
        }

        if (! is_array($uploaded)) {
            return [];
        }

        return array_values(array_filter($uploaded, fn (mixed $path): bool => is_string($path) && filled($path)));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function resolveOriginalFilenameForPath(array $data, string $uploadedPath): ?string
    {
        $fileNames = $data[MediaAssetUpload::FILE_NAMES_FIELD] ?? null;

        if (is_string($fileNames) && filled($fileNames)) {
            return basename($fileNames);
        }

        if (! is_array($fileNames)) {
            return null;
        }

        $fileName = $fileNames[$uploadedPath] ?? null;

        if (is_string($fileName) && filled($fileName)) {
            return basename($fileName);
        }

        $first = reset($fileNames);

        return is_string($first) && filled($first) ? basename($first) : null;
    }

    public function buildFolderPath(?string $parentFolder, string $name): string
    {
        $parentFolder = $this->normalizeFolder($parentFolder);
        $name = trim(trim($name), '/');

        return filled($parentFolder) ? $parentFolder . '/' . $name : $name;
    }

    public function ensureFolderExists(string $path): MediaAssetFolder
    {
        $path = $this->normalizeFolder($path);

        return MediaAssetFolder::query()->firstOrCreate(
            ['path' => $path],
            ['name' => basename($path)]
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createAsset(array $data, ?string $folder = null): MediaAsset
    {
        $uploadedPath = $this->resolveUploadedPaths($data)[0] ?? null;

        if (blank($uploadedPath)) {
            throw new InvalidArgumentException('Uploaded image is required.');
        }

        return $this->createAssetFromUploadedPath(
            $uploadedPath,
            $this->resolveOriginalFilenameForPath($data, $uploadedPath),
            $folder ?? ($data['folder'] ?? null),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<MediaAsset>
     */
    public function createAssets(array $data, ?string $folder = null): array
    {
        $uploadedPaths = $this->resolveUploadedPaths($data);

        if ($uploadedPaths === []) {
            throw new InvalidArgumentException('Uploaded image is required.');
        }

        return array_map(
            fn (string $uploadedPath): MediaAsset => $this->createAssetFromUploadedPath(
                $uploadedPath,
                $this->resolveOriginalFilenameForPath($data, $uploadedPath),
                $folder ?? ($data['folder'] ?? null),
            ),
            $uploadedPaths,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateAsset(MediaAsset $record, array $data): MediaAsset
    {
        $uploadedPath = $data[MediaAssetUpload::FIELD] ?? $record->path;
        $originalFilename = $this->resolveOriginalFilename($data);

        $attributes = [];

        if (array_key_exists('folder', $data)) {
            $attributes['folder'] = $this->normalizeFolder($data['folder']);
        }

        if (array_key_exists('disk', $data)) {
            $attributes['disk'] = filled($data['disk']) ? (string) $data['disk'] : MediaAssetUpload::DEFAULT_DISK;
        }

        $record->update($attributes);

        return $this->syncUploadedImage($record, $uploadedPath, $originalFilename);
    }

    public function syncUploadedImage(MediaAsset $mediaAsset, ?string $uploadedPath, ?string $originalFilename = null): MediaAsset
    {
        if (filled($uploadedPath) && $uploadedPath !== $mediaAsset->path) {
            $mediaAdder = $mediaAsset
                ->addMediaFromDisk($uploadedPath, $mediaAsset->disk)
                ->usingName($this->guessNameFromOriginalFilename($originalFilename ?? $uploadedPath));

            if (filled($originalFilename)) {
                $mediaAdder->usingFileName(basename($originalFilename));
            }

            $media = $mediaAdder->toMediaCollection(MediaAssetUpload::COLLECTION, $mediaAsset->disk);

            $this->cleanupTemporaryUpload($mediaAsset->disk, $uploadedPath, $media);
        }

        return $this->syncMetadata($mediaAsset);
    }

    public function syncMetadata(MediaAsset $mediaAsset): MediaAsset
    {
        $media = $mediaAsset->getFirstMedia(MediaAssetUpload::COLLECTION);

        if (! $media) {
            $mediaAsset->forceFill([
                'disk' => $mediaAsset->disk ?: MediaAssetUpload::DEFAULT_DISK,
                'path' => null,
                'public_url' => null,
            ])->saveQuietly();

            return $mediaAsset->refresh();
        }

        $name = $this->guessNameFromPath($media->file_name);

        if ($media->name !== $name) {
            $media->name = $name;
        }

        $media->setCustomProperty('folder', $mediaAsset->folder);
        $media->save();

        $mediaAsset->forceFill([
            'disk' => $media->disk ?: MediaAssetUpload::DEFAULT_DISK,
            'path' => $media->getPathRelativeToRoot(),
            'public_url' => $media->getUrl(),
        ])->saveQuietly();

        return $mediaAsset->refresh();
    }

    protected function cleanupTemporaryUpload(string $disk, string $uploadedPath, Media $media): void
    {
        if (! str_starts_with($uploadedPath, MediaAssetUpload::TEMP_DIRECTORY)) {
            return;
        }

        if ($uploadedPath === $media->getPathRelativeToRoot()) {
            return;
        }

        $storage = Storage::disk($disk);

        if ($storage->exists($uploadedPath)) {
            $storage->delete($uploadedPath);
        }
    }

    protected function createAssetFromUploadedPath(string $uploadedPath, ?string $originalFilename, ?string $folder = null): MediaAsset
    {
        $folder = $this->normalizeFolder($folder);

        $record = MediaAsset::query()->create([
            'folder' => $folder,
            'disk' => MediaAssetUpload::DEFAULT_DISK,
        ]);

        return $this->syncUploadedImage($record, $uploadedPath, $originalFilename);
    }
}

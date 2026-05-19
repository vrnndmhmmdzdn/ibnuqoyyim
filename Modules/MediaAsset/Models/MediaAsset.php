<?php

namespace Modules\MediaAsset\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Modules\MediaAsset\Support\MediaAssetUpload;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaAsset extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'media_assets';

    protected $fillable = [
        'folder',
        'disk',
        'path',
        'public_url',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(MediaAssetUpload::COLLECTION)
            ->useDisk(MediaAssetUpload::DEFAULT_DISK)
            ->singleFile()
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/avif',
                'image/svg+xml',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->performOnCollections(MediaAssetUpload::COLLECTION)
            ->fit(Fit::Crop, 320, 320)
            ->nonQueued();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->public_url ?: $this->getFirstMediaUrl(MediaAssetUpload::COLLECTION);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl(MediaAssetUpload::COLLECTION, 'thumb') ?: $this->image_url;
    }

    public function getDisplayFolderAttribute(): string
    {
        return filled($this->folder) ? $this->folder : 'root';
    }

    public function getFileNameAttribute(): ?string
    {
        $media = $this->getFirstMedia(MediaAssetUpload::COLLECTION);

        if (filled($media?->file_name)) {
            return $media->file_name;
        }

        return filled($this->path) ? basename($this->path) : null;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->file_name ?: 'Untitled Image';
    }

    public function getReadableFileNameAttribute(): string
    {
        $fileName = pathinfo($this->display_name, PATHINFO_FILENAME);

        return Str::title(str_replace(['-', '_'], ' ', $fileName));
    }
}

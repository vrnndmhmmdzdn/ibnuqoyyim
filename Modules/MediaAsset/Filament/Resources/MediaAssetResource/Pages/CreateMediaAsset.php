<?php

namespace Modules\MediaAsset\Filament\Resources\MediaAssetResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\MediaAsset\Filament\Resources\MediaAssetResource;
use Modules\MediaAsset\Services\MediaAssetService;
use Modules\MediaAsset\Support\MediaAssetUpload;

class CreateMediaAsset extends CreateRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function afterFill(): void
    {
        $folder = app(MediaAssetService::class)->normalizeFolder(request()->query('folder'));

        if (filled($folder)) {
            $this->form->fill([
                'folder' => $folder,
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $uploadedPath = $data[MediaAssetUpload::FIELD] ?? null;

        if (blank($uploadedPath)) {
            Notification::make()
                ->danger()
                ->title('Image wajib di-upload')
                ->send();

            $this->halt();
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(MediaAssetService::class)->createAsset($data);
    }

    protected function getRedirectUrl(): string
    {
        return MediaAssetResource::getUrl('edit', ['record' => $this->record]);
    }
}

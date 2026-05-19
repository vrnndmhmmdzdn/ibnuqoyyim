<?php

namespace Modules\MediaAsset\Filament\Resources\MediaAssetResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Modules\MediaAsset\Filament\Resources\MediaAssetResource;
use Modules\MediaAsset\Models\MediaAsset;
use Modules\MediaAsset\Services\MediaAssetService;
use Modules\MediaAsset\Support\MediaAssetUpload;

class EditMediaAsset extends EditRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data[MediaAssetUpload::FIELD] = $this->getRecord()->path;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $service = app(MediaAssetService::class);

        $data['folder'] = $service->normalizeFolder($data['folder'] ?? null);
        $data['disk'] = MediaAssetUpload::DEFAULT_DISK;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(MediaAssetService::class)->updateAsset($record, $data);
    }

    /**
     * @return MediaAsset
     */
    public function getRecord(): Model
    {
        /** @var MediaAsset $record */
        $record = parent::getRecord();

        return $record;
    }
}

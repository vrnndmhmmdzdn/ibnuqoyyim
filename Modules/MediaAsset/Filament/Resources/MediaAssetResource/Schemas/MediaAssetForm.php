<?php

namespace Modules\MediaAsset\Filament\Resources\MediaAssetResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\MediaAsset\Support\MediaAssetUpload;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make(MediaAssetUpload::FIELD)
                    ->label('Image')
                    ->image()
                    ->disk(MediaAssetUpload::DEFAULT_DISK)
                    ->directory(MediaAssetUpload::TEMP_DIRECTORY)
                    ->storeFileNamesIn(MediaAssetUpload::FILE_NAMES_FIELD)
                    ->visibility('public')
                    ->imagePreviewHeight('220')
                    ->openable()
                    ->downloadable(),
                TextInput::make('folder')
                    ->label('Folder')
                    ->maxLength(255)
                    ->placeholder('products atau banners/homepage'),
            ]);
    }
}

<?php

namespace Modules\MediaAsset\Support;

final class MediaAssetUpload
{
    public const COLLECTION = 'gallery';

    public const DEFAULT_DISK = 'public';

    public const FIELD = 'uploaded_image';

    public const FILE_NAMES_FIELD = 'uploaded_image_file_names';

    public const TEMP_DIRECTORY = 'media-assets/tmp';

    private function __construct() {}
}

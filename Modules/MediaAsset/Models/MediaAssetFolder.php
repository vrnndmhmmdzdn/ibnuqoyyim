<?php

namespace Modules\MediaAsset\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAssetFolder extends Model
{
    protected $table = 'media_asset_folders';

    protected $fillable = [
        'name',
        'path',
    ];
}

<?php

namespace Modules\MediaAsset;

use Illuminate\Support\ServiceProvider;

class MediaAssetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'media-asset');
    }
}

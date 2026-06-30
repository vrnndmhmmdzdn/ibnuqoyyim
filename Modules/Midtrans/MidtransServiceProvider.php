<?php

namespace Modules\Midtrans;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Modules\Midtrans\Services\MidtransService;

class MidtransServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/Config/midtrans.php', 'midtrans');
        
        // Register MidtransService as singleton
        $this->app->singleton(MidtransService::class, function ($app) {
            return new MidtransService();
        });
    }

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'midtrans');
        
        // Register routes
        $this->registerRoutes();
        
        // Register Filament Resources and Pages
        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    \Modules\Midtrans\Filament\Resources\MidtransCredentialResource::class,
                    \Modules\Midtrans\Filament\Resources\MidtransTransactionResource::class,
                ]);
                
                Filament::registerPages([
                    \Modules\Midtrans\Filament\Pages\MidtransDemo::class,
                ]);
            });
        }
    }

    /**
     * Register routes.
     */
    protected function registerRoutes(): void
    {
        if (file_exists(__DIR__ . '/Routes/web.php')) {
            Route::middleware('web')
                ->group(__DIR__ . '/Routes/web.php');
        }
        
        if (file_exists(__DIR__ . '/Routes/api.php')) {
            Route::middleware('api')
                ->prefix('api')
                ->group(__DIR__ . '/Routes/api.php');
        }
    }
}

<?php

namespace Modules\DashboardBuilder;

use Illuminate\Support\ServiceProvider;

class DashboardBuilderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'dashboard-builder');
    }
}

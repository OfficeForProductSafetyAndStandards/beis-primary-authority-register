<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DashboardStatsService;

class DashboardStatsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('dss', function ($app) {
            return new DashboardStatsService();
        });
    }
}

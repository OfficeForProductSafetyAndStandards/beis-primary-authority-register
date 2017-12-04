<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BuildVersionStatsService;

class BuildVersionServiceProvider extends ServiceProvider
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
        $this->app->bind('bvs', function ($app) {
            return new BuildVersionService();
        });
    }
}

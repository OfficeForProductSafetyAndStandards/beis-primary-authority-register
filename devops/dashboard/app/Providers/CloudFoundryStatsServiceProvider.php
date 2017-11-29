<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CloudFoundryStatsService;

class CloudFoundryStatsServiceProvider extends ServiceProvider
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
        $this->app->bind('cfss', function ($app) {
            return new CloudFoundryStatsService();
        });
    }
}

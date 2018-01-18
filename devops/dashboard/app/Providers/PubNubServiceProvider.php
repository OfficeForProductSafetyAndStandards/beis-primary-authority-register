<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CloudFoundryStatsService;

class PubNubServiceProvider extends ServiceProvider
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
        $this->app->bind('pns', function ($app) {
            return new PubNubService();
        });
    }
}

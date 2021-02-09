<?php
namespace Mariojgt\Connect;

use Illuminate\Support\ServiceProvider;
use Mariojgt\Connect\Events\UserVerifyEvent;
use Mariojgt\Connect\Listeners\SendUserVerifyListener;
use Illuminate\Support\Facades\Event;

class ConnectProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load connect views
        //$this->loadViewsFrom(__DIR__.'/views', 'connect');
        // Load connect routes
        //$this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        //$this->loadRoutesFrom(__DIR__.'/Routes/auth.php');
        // Load Migrations
        //$this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publish();
    }

    public function publish()
    {
        // Publish the npm case we need to do soem developent
        // $this->publishes([
        //     __DIR__.'/../Publish/Npm/' => base_path()
        // ]);
    }
}

<?php 

namespace Laraversion\Laraversion;

use Illuminate\Support\ServiceProvider;
use Laraversion\Laraversion\Commands\LaraversionCommand;
use Laraversion\Laraversion\Commands\ListVersionsCommand;

class LaraversionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laraversion.php' => config_path('laraversion.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                LaraversionCommand::class,
                ListVersionsCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laraversion.php', 'laraversion'
        );
    }
}

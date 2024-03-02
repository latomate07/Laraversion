<?php
namespace Laraversion\Laraversion;

use Illuminate\Support\ServiceProvider;
use Laraversion\Laraversion\Commands\LaraversionCommand;

class LaraversionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laraversion.php' => config_path('laraversion.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                LaraversionCommand::class,
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

<?php

namespace Laraversion\Laraversion;

use Illuminate\Support\ServiceProvider;
use Laraversion\Laraversion\Commands\CompareVersionsCommand;
use Laraversion\Laraversion\Commands\LaraversionCommand;
use Laraversion\Laraversion\Commands\ListVersionsCommand;
use Laraversion\Laraversion\Commands\InstallGuiCommand;
use Illuminate\Support\Facades\Route;

/**
 * Laraversion Service Provider
 *
 * This service provider registers the Laraversion package with the Laravel application.
 */
class LaraversionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->registerCommands();

        // Load views and routes only if the config option is set
        if (config('laraversion.load_views_and_routes', true)) {
            $this->loadViews();
            $this->loadRoutes();
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laraversion.php',
            'laraversion'
        );
    }

    /**
     * Publish the package configuration file.
     *
     * @return void
     */
    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/laraversion.php' => config_path('laraversion.php'),
        ], 'config');
    }

    /**
     * Publish the package migration files.
     *
     * @return void
     */
    protected function publishMigrations()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Load the package views.
     *
     * @return void
     */
    protected function loadViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laraversion');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/laraversion'),
        ], 'views');
    }

    /**
     * Load the package routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $this->registerMiddleware();
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    /**
     * Register the package commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                LaraversionCommand::class,
                ListVersionsCommand::class,
                CompareVersionsCommand::class,
            ]);

            $this->registerInstallGuiCommand();
        }
    }

    /**
     * Register the InstallGuiCommand.
     *
     * @return void
     */
    protected function registerInstallGuiCommand()
    {
        $this->app->singleton(InstallGuiCommand::class, function ($app) {
            return new InstallGuiCommand($app['files']);
        });

        $this->commands(InstallGuiCommand::class);
    }

    /**
     * Register the middleware for Laraversion routes.
     *
     * @return void
     */
    protected function registerMiddleware()
    {
        $middleware = config('laraversion.middleware');

        Route::middlewareGroup('laraversion', $middleware);
    }
}

<?php

namespace Loonpwn\Swiftype;

use Illuminate\Support\ServiceProvider;
use Loonpwn\Swiftype\Console\Commands\PurgeAllDocuments;

class SwiftypeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/swiftype.php', 'swiftype');

        // Register the service the package provides.
        $this->app->singleton('swiftype', function () {
            return new Api();
        });

        // Register the service the package provides.
        $this->app->bind('swiftype-engine', function () {
            return new Engine();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['swiftype'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/swiftype.php' => config_path('swiftype.php'),
        ], 'swiftype-config');

        // Registering package commands.
        $this->commands([PurgeAllDocuments::class]);
    }
}

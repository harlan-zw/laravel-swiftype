<?php

namespace Loonpwn\Swiftype;

use Illuminate\Support\ServiceProvider;
use Loonpwn\Swiftype\Clients\Api;
use Loonpwn\Swiftype\Clients\Engine;
use Loonpwn\Swiftype\Console\Commands\PurgeDocuments;
use Loonpwn\Swiftype\Console\Commands\SyncDocuments;
use Loonpwn\Swiftype\Facades\Swiftype;
use Loonpwn\Swiftype\Facades\SwiftypeEngine;

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

        $this->app->singleton(Swiftype::class, function () {
            return Api::build();
        });

        // Register the service the package provides.
        $this->app->bind(SwiftypeEngine::class, function () {
            return new Engine(
                $this->app->get(Swiftype::class)
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'swiftype',
        ];
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
            dirname(__DIR__).'/config/swiftype.php' => config_path('swiftype.php'),
        ], 'swiftype-config');

        // Registering package commands.
        $this->commands([
            PurgeDocuments::class,
            SyncDocuments::class,
        ]);
    }
}

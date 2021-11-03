<?php

namespace SmartBit\TemplateMaker\Providers;

use Illuminate\Support\ServiceProvider;

class TemplateMakerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'document-template-editor');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'template-maker');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('template-maker.php'),
            ], 'template-maker-config');

            // Publishing the views.
            // $this->publishes([
            //     __DIR__.'/../resources/views' => resource_path('views/vendor/TemplateMaker'),
            // ], 'views');

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('resources/template-maker'),
            ], 'assets');

            // Command To publish files
            // php artisan vendor:publish --tag=public --force

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/document-template-editor'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }


    /**
     * Register the application services.
     */
    public function register()
    {
//         // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'template-maker');

//         // Register the main class to use with the facade
//         $this->app->singleton('template-maker', function () {
//             return new TemplateMaker;
//         });
    }
}
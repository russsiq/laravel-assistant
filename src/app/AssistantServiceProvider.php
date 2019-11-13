<?php

namespace Russsiq\Assistant;

use Illuminate\Support\ServiceProvider;

class AssistantServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'assistant');
        // echo trans('assistant::messages.welcome');
        // $this->publishes([
        //     __DIR__.'/../resources/lang' => resource_path('lang/vendor/assistant'),
        // ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'assistant');
        // return view('assistant::admin');
        // $this->publishes([
        //     __DIR__.'/../resources/views' => resource_path('views/vendor/assistant'),
        // ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function provides()
    {
        //
    }
}

<?php

namespace Russsiq\Assistant;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AssistantServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Указывает, что загрузка поставщика отложенная.
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
     * Регистрация Ассистента как поставщика служб.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Получить службы, предоставляемые Ассистентом.
     *
     * @return array
     */
    public function provides()
    {
        return [
            //
        ];
    }
}

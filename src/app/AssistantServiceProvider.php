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
        // 
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

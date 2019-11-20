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
        $sourceDir = __DIR__.'/../';

        $this->loadAssistantFiles($sourceDir);

        // Публикация ресурсов может быть выполнена только из консоли.
        if ($this->app->runningInConsole()) {
            $this->publishAssistantFiles($sourceDir);
        }
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

    /**
     * Загрузка файлов Ассистента.
     *
     * @param  string $sourceDir
     * @return void
     */
    protected function loadAssistantFiles(string $sourceDir)
    {
        $this->loadRoutesFrom($sourceDir.'routes/web.php');
        $this->loadJsonTranslationsFrom($sourceDir.'resources/lang');
        // $this->loadTranslationsFrom($sourceDir.'resources/lang', 'assistant');
        $this->loadViewsFrom($sourceDir.'resources/views', 'assistant');
    }

    /**
     * Публикация файлов Ассистента.
     * `php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider"`
     *
     * @param  string $sourceDir
     * @return void
     */
    protected function publishAssistantFiles(string $sourceDir)
    {
        // php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=config --force
        $this->publishes([
            $sourceDir.'config/assistant.php' => config_path('assistant.php'),
        ], 'config');

        // php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=lang --force
        $this->publishes([
            $sourceDir.'resources/lang' => resource_path('lang/vendor/assistant'),
        ], 'lang');

        // php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=views --force
        $this->publishes([
            $sourceDir.'resources/views' => resource_path('views/vendor/assistant'),
        ], 'views');
    }
}

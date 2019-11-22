<?php

namespace Russsiq\Assistant;

use Russsiq\Assistant\Http\Middleware\CheckEnvFileExists;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AssistantServiceProvider extends ServiceProvider// implements DeferrableProvider
{
    /**
     * Путь до директории с исходниками.
     *
     * @var string
     */
    const SOURCE_DIR = __DIR__.'/../';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->setAssistantMiddlewareGroup();

        $this->loadAssistantFiles();

        // Действия, выполнение которых может быть только из консоли.
        if ($this->app->runningInConsole()) {
            // Публикация ресурсов.
            $this->publishAssistantFiles();
        }
    }

    /**
     * Регистрация Ассистента как поставщика служб.
     *
     * @return void
     */
    public function register()
    {
        // $this->mergeConfigFrom(self::SOURCE_DIR.'config/assistant.php', 'assistant');
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
     * Установка посредников группы `web`.
     *
     * @return void
     */
    protected function setAssistantMiddlewareGroup()
    {
        Route::prependMiddlewareToGroup('web', CheckEnvFileExists::class);
    }

    /**
     * Загрузка файлов Ассистента.
     *
     * @return void
     */
    protected function loadAssistantFiles()
    {
        $this->loadRoutesFrom(self::SOURCE_DIR.'routes/web.php');
        $this->loadJsonTranslationsFrom(self::SOURCE_DIR.'resources/lang');
        // $this->loadTranslationsFrom(self::SOURCE_DIR.'resources/lang', 'assistant');
        $this->loadViewsFrom(self::SOURCE_DIR.'resources/views', 'assistant');
    }

    /**
     * Публикация файлов Ассистента.
     * `php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider"`
     *
     * @return void
     */
    protected function publishAssistantFiles()
    {
        // php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=config --force
        $this->publishes([
            self::SOURCE_DIR.'config/assistant.php' => config_path('assistant.php'),
        ], 'config');

        // php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=lang --force
        $this->publishes([
            self::SOURCE_DIR.'resources/lang' => resource_path('lang/vendor/assistant'),
        ], 'lang');

        // php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=views --force
        $this->publishes([
            self::SOURCE_DIR.'resources/views' => resource_path('views/vendor/assistant'),
        ], 'views');
    }
}

<?php

namespace Russsiq\Assistant;

use Russsiq\Assistant\Commands\BeforeInstalledMakeCommand;
use Russsiq\Assistant\Http\Middleware\AlreadyInstalled;
use Russsiq\Assistant\Http\Middleware\CheckEnvFileExists;
use Russsiq\Assistant\Support\Archivist;
use Russsiq\Assistant\Support\Cleaner;
use Russsiq\Assistant\Support\Installer;
use Russsiq\Assistant\Support\Updater;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AssistantServiceProvider extends ServiceProvider // implements DeferrableProvider
{
    /**
     * Все синглтоны (одиночки) контейнера,
     * которые должны быть зарегистрированы.
     *
     * @var array
     */
    public $singletons = [
        'laravel-archivist' => Archivist::class,
        'laravel-cleaner' => Cleaner::class,
        'laravel-installer' => Installer::class,
        'laravel-updater' => Updater::class,
    ];

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

            // Регистрация команд консоли Artisan.
            $this->registerAssistantCommands();
        }
    }

    /**
     * Регистрация Ассистента как поставщика служб.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(self::SOURCE_DIR.'config/assistant.php', 'assistant');
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

        Route::middlewareGroup('already-installed', [
            AlreadyInstalled::class
        ]);
    }

    /**
     * Загрузка файлов Ассистента.
     *
     * @return void
     */
    protected function loadAssistantFiles()
    {
        $this->loadRoutesFrom(self::SOURCE_DIR.'routes/web.php');
        $this->loadTranslationsFrom(self::SOURCE_DIR.'resources/lang', 'assistant');
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

    /**
     * Регистрация команд консоли Artisan.
     *
     * @return void
     */
    protected function registerAssistantCommands()
    {
        $this->commands([
            BeforeInstalledMakeCommand::class,
        ]);
    }
}

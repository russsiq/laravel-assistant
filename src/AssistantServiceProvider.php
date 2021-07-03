<?php

namespace Russsiq\Assistant;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Russsiq\Assistant\Commands\BeforeInstalledMakeCommand;
use Russsiq\Assistant\Http\Middleware\AlreadyInstalled;
use Russsiq\Assistant\Http\Middleware\CheckEnvFileExists;
use Russsiq\Assistant\Support\Archivist\ArchivistManager;
use Russsiq\Assistant\Support\Cleaner\CleanerManager;
use Russsiq\Assistant\Support\Installer\InstallerManager;
use Russsiq\Assistant\Support\Updater\UpdaterManager;

class AssistantServiceProvider extends ServiceProvider
{
    /**
     * Short package name.
     *
     * @const string
     */
    const PACKAGE_NAME = 'laravel-assistant';

    /**
     * Package root directory.
     *
     * @const string
     */
    const PACKAGE_DIR = __DIR__.'/../';

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        'laravel-archivist' => ArchivistManager::class,
        'laravel-cleaner' => CleanerManager::class,
        'laravel-installer' => InstallerManager::class,
        'laravel-updater' => UpdaterManager::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configureAssistant();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureAssistantLoadableFiles();

        if ($this->app->runningInConsole()) {
            $this->configureAssistantPublishing();
            $this->configureAssistantCommands();
        }

        $this->setAssistantMiddlewareGroup();
    }

    /**
     * Setup the configuration for the package.
     *
     * @return void
     */
    protected function configureAssistant(): void
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/assistant.php'), 'assistant'
        );
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @cmd `php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider"`
     * @return void
     */
    protected function configureAssistantPublishing(): void
    {
        // @cmd `php artisan vendor:publish --tag=assistant-config`
        $this->publishes([
            $this->packagePath('config/assistant.php') => config_path('assistant.php'),
        ], 'assistant-config');

        // @cmd `php artisan vendor:publish --tag=assistant-lang`
        $this->publishes([
            $this->packagePath('resources/lang') => resource_path('lang/vendor/assistant'),
        ], 'assistant-lang');

        // @cmd `php artisan vendor:publish --tag=assistant-views`
        $this->publishes([
            $this->packagePath('resources/views') => resource_path('views/vendor/assistant'),
        ], 'assistant-views');
    }

    /**
     * Configure the commands offered by the package.
     *
     * @return void
     */
    protected function configureAssistantCommands(): void
    {
        $this->commands([
            BeforeInstalledMakeCommand::class,
        ]);
    }

    /**
     * Configure the loadable files offered by the package.
     *
     * @return void
     */
    protected function configureAssistantLoadableFiles(): void
    {
        $this->loadRoutesFrom($this->packagePath('routes/web.php'));
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'assistant');
        $this->loadViewsFrom($this->packagePath('resources/views'), 'assistant');
    }

    /**
     * Set Assistant middleware group.
     *
     * @return void
     */
    protected function setAssistantMiddlewareGroup(): void
    {
        Route::prependMiddlewareToGroup('web', CheckEnvFileExists::class);

        Route::middlewareGroup('already-installed', [
            AlreadyInstalled::class,
        ]);
    }

    /**
     * Get the path to the package folder.
     *
     * @param  string  $path
     * @return string
     */
    protected function packagePath(string $path): string
    {
        return self::PACKAGE_DIR.$path;
    }
}

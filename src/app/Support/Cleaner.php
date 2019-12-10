<?php

namespace Russsiq\Assistant\Support;

use Artisan;

use Illuminate\Foundation\Application;

use Russsiq\Assistant\Support\Contracts\CleanerContract;

class Cleaner implements CleanerContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр Оптимизатора.
     *
     * @param  Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Очистка кэша приложения.
     *
     * @return void
     */
    public function clearCache()
    {
        // code...
    }

    /**
     * Очистка кэша настроек приложения.
     *
     * @return void
     */
    public function clearConfig()
    {
        // code...
    }

    /**
     * Очистка кэша маршрутов приложения.
     *
     * @return void
     */
    public function clearRoute()
    {
        // code...
    }

    /**
     * Очистка скомпилированных шаблонов приложения.
     *
     * @return void
     */
    public function clearView()
    {
        // code...
    }

    /**
     * Комплексная очистка и последующее кэширование.
     *
     * @return void
     */
    public function complexOptimize()
    {
        // code...
    }

    /**
     * Запустить команду консоли Artisan.
     *
     * @param  string $name [description]
     *
     * @return string
     */
    protected function artisanCall(string $name): string
    {
        Artisan::call($name);

        return Artisan::output();
    }
}

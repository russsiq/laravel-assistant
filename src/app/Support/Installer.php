<?php

namespace Russsiq\Assistant\Support;

use Illuminate\Foundation\Application;

use Russsiq\Assistant\Support\Contracts\InstallerContract;

class Installer implements InstallerContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр Установщика приложения.
     *
     * @param  Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        dump('INSTALLER');
    }

    /**
     * Инициировать начальный этап установки.
     *
     * @return void
     */
    public function initiate()
    {
        // code...
    }

    /**
     * Получить массив с минимальными требованиями.
     *
     * @return array
     */
    public function requirements(): array
    {
        // code...
    }

    /**
     * Получить массив "зловредных" переменных.
     *
     * @return array
     */
    public function antiGlobals(): array
    {
        // code...
    }

    /**
     * Получить массив прав на доступ к директориям.
     *
     * @return array
     */
    public function filePermissions(): array
    {
        // code...
    }

    /**
     * Получить массив доступных при установке тем.
     *
     * @return array
     */
    public function themes(): array
    {
        // code...
    }
}

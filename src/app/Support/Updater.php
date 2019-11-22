<?php

namespace Russsiq\Assistant\Support;

use Illuminate\Foundation\Application;

use Russsiq\Assistant\Support\Contracts\UpdaterContract;

class Updater implements UpdaterContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр Мастера обновлений.
     *
     * @param  Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        dump('UPDATER');
    }
}

<?php

namespace Russsiq\Assistant\Support;

use Illuminate\Foundation\Application;

use Russsiq\Assistant\Support\Contracts\CleanerContract;

class Cleaner implements CleanertContract
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

        dump('CLEANER');
    }
}

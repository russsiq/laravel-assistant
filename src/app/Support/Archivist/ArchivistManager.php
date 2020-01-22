<?php

namespace Russsiq\Assistant\Support\Archivist;

use Illuminate\Foundation\Application;

use Russsiq\Assistant\Contracts\ArchivistContract;

class ArchivistManager implements ArchivistContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр Архивариуса приложения.
     *
     * @param  Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        dump('ARCHIVIST');
    }

    /**
     * Создать резервную копию.
     *
     * @return void
     */
    public function backup()
    {
        // code...
    }

    /**
     * Восстановить резервную копию.
     *
     * @return void
     */
    public function restore()
    {
        // code...
    }
}

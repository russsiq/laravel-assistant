<?php

namespace Russsiq\Assistant\Facades;

// Сторонние зависимости.
use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Contracts\InstallerContract
 * @see \Russsiq\Assistant\Support\Installer
 */
class Installer extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-installer';
    }
}

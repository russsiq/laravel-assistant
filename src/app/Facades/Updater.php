<?php

namespace Russsiq\Assistant\Facades;

// Сторонние зависимости.
use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Contracts\UpdaterContract
 * @see \Russsiq\Assistant\Support\Updater
 */
class Updater extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-updater';
    }
}

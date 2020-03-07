<?php

namespace Russsiq\Assistant\Facades;

// Сторонние зависимости.
use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Contracts\ArchivistContract
 * @see \Russsiq\Assistant\Support\Archivist
 */
class Archivist extends Facade
{
    /**
     * Имя ключа оператора операций.
     * @const string
     */
    const KEY_NAME_OPERATOR = 'operator';

    /**
     * Получить зарегистрированное имя компонента.
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-archivist';
    }
}

<?php

namespace Russsiq\Assistant\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Contracts\CleanerContract
 * @see \Russsiq\Assistant\Support\Cleaner
 */
class Cleaner extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     * 
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-cleaner';
    }
}

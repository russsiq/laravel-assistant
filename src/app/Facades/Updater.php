<?php

namespace Russsiq\Assistant\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Support\Contracts\UpdaterContract
 * @see \Russsiq\Assistant\Support\Updater
 */
class Updater extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-updater';
    }
}

<?php

namespace Russsiq\Assistant\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Support\Contracts\CleanerContract
 * @see \Russsiq\Assistant\Support\Cleaner
 */
class Cleaner extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-cleaner';
    }
}

<?php

namespace Russsiq\Assistant\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Support\Contracts\ArchivistContract
 * @see \Russsiq\Assistant\Support\Archivist
 */
class Archivist extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-archivist';
    }
}

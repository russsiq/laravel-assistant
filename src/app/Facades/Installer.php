<?php

namespace Russsiq\Assistant\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Contracts\InstallerContract
 * @see \Russsiq\Assistant\Support\Installer
 */
class Installer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-installer';
    }
}

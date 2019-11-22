<?php

namespace Russsiq\Assistant\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\Assistant\Support\Contracts\InstallerContract
 * @see \Russsiq\Assistant\Support\Installer
 */
class Installer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-installer';
    }
}
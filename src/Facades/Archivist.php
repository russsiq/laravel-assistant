<?php

namespace Russsiq\Assistant\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Str;

/**
 * @see \Russsiq\Assistant\Contracts\ArchivistContract
 * @see \Russsiq\Assistant\Support\Archivist
 */
class Archivist extends Facade
{
    /**
     * Имя ключа оператора операций.
     * 
     * @const string
     */
    const KEY_NAME_OPERATOR = 'operator';

    /**
     * Расширение файла резервной копии.
     * 
     * @const string
     */
    const FILE_EXTENSION_BACKUP = 'zip';

    /**
     * Имя файла с резервной копией базы данных.
     * 
     * @const string
     */
    const DATABASE_FILENAME = 'database_backup';

    /**
     * Получить зарегистрированное имя компонента.
     * 
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-archivist';
    }

    /**
     * Сгенерировать новое имя файла для резервной копии.
     * 
     * @return string
     */
    public static function generateBackupFilename(): string
    {
        return date('Y_m_d_His')
            .'_backup_'
            .Str::slug(config('app.name'))
            .'.'
            .Archivist::FILE_EXTENSION_BACKUP;
    }
}

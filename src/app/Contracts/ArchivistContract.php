<?php

namespace Russsiq\Assistant\Contracts;

/**
 * Контракт публичных методов Архивариуса.
 * @var interface
 */
interface ArchivistContract
{
    /**
     * Создать резервную копию.
     * @return void
     */
    public function backup();

    /**
     * Получить коллекцию файлов с архивами.
     * @return array
     */
    public function backups();

    /**
     * Восстановить резервную копию.
     * @return void
     */
    public function restore();
}
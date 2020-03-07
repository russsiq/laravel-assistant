<?php

namespace Russsiq\Assistant\Contracts;

/**
 * Контракт публичных методов Архивариуса.
 * @var interface
 */
interface ArchivistContract
{
    /**
     * Создать резервную копию в соответствии с выбранными опциями.
     * @param  array  $options
     * @return void
     */
    public function backup(array $options = []);

    /**
     * Получить коллекцию файлов резервных копий,
     * включая их свойства: имя, размер, дата создания.
     * @return array
     */
    public function backups();

    /**
     * Восстановить резервную копию в соответствии с выбранными опциями.
     * @param  string  $filename
     * @param  array  $options
     * @return void
     */
    public function restore(string $filename, array $options = []);

    /**
     * Удалить файл резервной копии.
     * @param  string  $filename
     * @return bool
     */
    public function deleteBackup(string $filename);

    /**
     * Удалить все файлы резервных копий.
     * @return bool
     */
    public function deleteAllBackups();
}

<?php

namespace Russsiq\Assistant\Contracts;

/**
 * Контракт публичных методов Архивариуса.
 * @var interface
 */
interface ArchivistContract
{
    /**
     * Установить массив параметров.
     * @param  array  $options
     * @return mixed
     */
    public function setOptions(array $options);

    /**
     * Запустить архивирование / восстановление.
     * @return mixed
     */
    public function execute();

    /**
     * Получить коллекцию файлов резервных копий,
     * включая их свойства: имя, размер, дата создания.
     * @return array
     */
    public function backups();

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

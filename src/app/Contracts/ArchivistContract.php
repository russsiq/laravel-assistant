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
     * @return self
     */
    public function setOptions(array $options): self;

    /**
     * Установить операции, которые должны будут выполнены.
     * @param  mixed  $options
     * @return self
     */
    public function with($options): self;

    /**
     * Убрать операции из списка запланированных.
     * @param  mixed  $options
     * @return self
     */
    public function without($options): self;

    /**
     * Запустить архивирование / восстановление.
     * @return mixed
     */
    public function execute();

    /**
     * Получить коллекцию файлов резервных копий,
     * включая их свойства: имя, размер, дата создания.
     * @return object
     */
    public function backups(): object;

    /**
     * Удалить файл резервной копии.
     * @param  string  $filename
     * @return bool
     */
    public function deleteBackup(string $filename): bool;

    /**
     * Удалить все файлы резервных копий.
     * @return bool
     */
    public function deleteAllBackups(): bool;
}

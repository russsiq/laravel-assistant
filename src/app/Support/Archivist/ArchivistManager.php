<?php

namespace Russsiq\Assistant\Support\Archivist;

// Исключения.

// Базовые расширения PHP.

// Зарегистрированные фасады приложения.

// Сторонние зависимости.
use Illuminate\Foundation\Application;
use Russsiq\Assistant\Contracts\ArchivistContract;

class ArchivistManager implements ArchivistContract
{
    /**
     * Экземпляр приложения.
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр Архивариуса приложения.
     * @param  Application  $app
     */
    public function __construct(
        Application $app
    ) {
        $this->app = $app;
    }

    /**
     * Создать резервную копию в соответствии с выбранными опциями.
     * @param  array  $options
     * @return void
     */
    public function backup(array $options = [])
    {

    }

    /**
     * Получить коллекцию файлов резервных копий,
     * включая их свойства: имя, размер, дата создания.
     * @return array
     */
    public function backups()
    {

    }

    /**
     * Восстановить резервную копию в соответствии с выбранными опциями.
     * @param  string  $filename
     * @param  array  $options
     * @return void
     */
    public function restore(string $filename, array $options = [])
    {

    }

    /**
     * Удалить файл резервной копии.
     * @param  string  $filename
     * @return bool
     */
    public function deleteBackup(string $filename)
    {

    }

    /**
     * Удалить все файлы резервных копий.
     * @return bool
     */
    public function deleteAllBackups()
    {

    }
}

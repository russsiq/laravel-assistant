<?php

namespace Russsiq\Assistant\Support\Archivist;

// Исключения.
use InvalidArgumentException;

// Зарегистрированные фасады приложения.
use Russsiq\Assistant\Facades\Archivist;

// Сторонние зависимости.
use Russsiq\Assistant\Contracts\ArchivistContract;
use Russsiq\Assistant\Contracts\Archivist\CanBackup;
use Russsiq\Assistant\Services\Zipper;
use Russsiq\Assistant\Support\Archivist\AbstractArchivist;

/**
 * Экземпляр Упаковщика.
 */
class Packager extends AbstractArchivist implements CanBackup
{
    /**
     * Путь к рабочей папке, содержащей архивы приложения.
     * @var string
     */
    protected $storePath;

    /**
     * Полный путь к файлу резервной копии.
     * @var string
     */
    protected $filename;

    /**
     * Массовое задание параметров архивирования / восстановления.
     * @param  array  $options
     * @return mixed
     */
    public function setOptions(array $options): ArchivistContract
    {
        if (empty($options['backup'])) {
            throw new InvalidArgumentException(
                "Action [backup] is not defined."
            );
        }

        return parent::setOptions($options['backup']);
    }

    /**
     * Запустить архивирование / восстановление.
     * @return mixed
     */
    public function execute()
    {
        return $this->backup();
    }

    /**
     * Задать рабочий файл резервной копии.
     * @param  string  $filename
     * @return self
     */
    public function to(string $filename): CanBackup
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Создать резервную копию в соответствии с выбранными опциями.
     * @return void
     */
    public function backup()
    {
        $messages = [];

        if (! $this->filename) {
            $this->to($this->generateBackupFilename());
        }

        $filename = $this->storePath($this->filename);
        // $this->ensureBackupDoesntAlreadyExist($name, $path);

        // Создание архива.
        $ziparchive = $this->ziparchive->create($filename);

        if (in_array('database', $this->options)) {
            // Создание дампа Базы Данных.
            //

            // Добавление дампа БД в архив.
            //

            // Удаление опции БД из списка запланированных.
            $this->without('database');
        }

        // Добавление директорий в архив.
        $this->backupDirectories($ziparchive, $this->options);

        // Добавление корневых файлов в архив.
        $this->backupFiles($ziparchive, $this->allowedFiles());

        // Закрытие архива.
        $ziparchive->close();

        $messages[] = 'Success';

        // Возвращение массива текстовых сообщений о выполненных операциях.
        return $messages;
    }

    protected function backupDirectories(Zipper $ziparchive, array $options = [])
    {
        foreach ($options as $option) {
            foreach ($this->directories($option) as $action => $directories) {
                foreach ($directories as $directory) {
                    switch ($action) {
                        case 'include':
                            $ziparchive->addDirectory($this->basePath($directory), $directory);
                            break;

                        case 'exclude':
                            $ziparchive->deleteDirectory($directory, true);
                            break;

                        case 'create':
                            $ziparchive->addEmptyDirectory($directory);
                            break;

                        default:
                            // code...
                            break;
                    }
                }
            }
        }
    }

    protected function backupFiles(Zipper $ziparchive, array $files = [])
    {
        foreach ($files as $file) {
            $ziparchive->addFile($this->basePath($file), $file);
        }
    }

    /**
     * Сгенерировать новое имя файла для резервной копии.
     * @return string
     */
    public function generateBackupFilename(): string
    {
        return date('Y_m_d_His')
            .'_backup_'
            .str_slug(config('app.name'))
            .'.'
            .Archivist::FILE_EXTENSION_BACKUP;
    }
}

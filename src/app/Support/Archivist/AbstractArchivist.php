<?php

namespace Russsiq\Assistant\Support\Archivist;

// Исключения.
use Exception;
use InvalidArgumentException;
use RuntimeException;

// Базовые расширения PHP.
use SplFileInfo;
use ZipArchive;

// Зарегистрированные фасады приложения.
use File;
use Russsiq\Assistant\Facades\Archivist;

// Сторонние зависимости.
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Russsiq\Assistant\Contracts\ArchivistContract;
use Symfony\Component\Finder\Finder;

/**
 * Абстрактная реализация Архивариуса.
 */
abstract class AbstractArchivist implements ArchivistContract
{
    /**
     * Экземпляр класса по работе с файловой системой.
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Экземляр класса по работе с архивами.
     * @var ZipArchive
     */
    protected $ziparchive;

    /**
     * Путь к рабочей папке, содержащей архивы приложения.
     * @var string
     */
    protected $storePath;

    /**
     * Массив запланированных операций,
     * которые должны быть выполнены.
     * @var array
     */
    protected $options = [];

    /**
     * Создать экземпляр.
     */
    public function __construct(
        Filesystem $filesystem,
        ZipArchive $ziparchive,
        array $config
    ) {
        $this->filesystem = $filesystem;
        $this->ziparchive = $ziparchive;

        $this->storePath = $config['store_path'];
    }

    /**
     * Получить директорию, где расположены резервные копии приложения.
     * @return string
     */
    protected function storePath(string $path = null): string
    {
        return $this->storePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Установить массив параметров.
     * @param  array  $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option) {
            $this->with($option);
        }

        return $this;
    }

    /**
     * Запустить архивирование / восстановление с запланированными операциями.
     * @param  array  $options
     * @return mixed
     */
    abstract public function execute();

    /**
     * Установить операции, которые должны будут выполнены.
     * @param  mixed  $options
     * @return self
     */
    public function with($options)
    {
        $options = is_string($options) ? func_get_args() : $options;

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Убрать опцию из списка запланированных.
     * @param  mixed  $options
     * @return self
     */
    public function without($options)
    {
        $this->options = array_diff_key($this->options, array_flip(
            is_string($options) ? func_get_args() : $options
        ));

        return $this;
    }

    /**
     * Получить коллекцию файлов резервных копий,
     * включая их свойства: имя, размер, дата создания.
     * @return array
     */
    public function backups()
    {
        $files = $this->filesystem->allFiles($this->storePath());

        return collect($files)
            ->filter(function (SplFileInfo $file, int $index) {
                return $file->isReadable()
                    && $file->getExtension() === trim(Archivist::FILE_EXTENSION_BACKUP, '\.');
            })
            ->sortByDesc(function (SplFileInfo $file, int $index) {
                return $file->getBasename();
            })
            ->transform(function (SplFileInfo $file, int $index) {
                return (object) [
                    'basename' => $file->getBasename(),
                    'size' => self::formatSize($file->getSize()),

                ];
            })
            ->values();
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

    public static function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $i).' '.$units[$i];
    }
}

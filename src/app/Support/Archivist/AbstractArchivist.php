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
use Russsiq\Assistant\Services\Zipper;
use Symfony\Component\Finder\Finder;

// use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryContract;

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
     * Экземпляр класса по работе с архивами.
     * @var Zipper
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
     * Массив настроек.
     * @var array
     */
    protected $config = [];

    /**
     * Создать экземпляр.
     */
    public function __construct(
        Filesystem $filesystem,
        Zipper $ziparchive,
        array $config
    ) {
        $this->filesystem = $filesystem;
        $this->ziparchive = $ziparchive;

        $this->config = $config;
    }

    /**
     * Получить директорию, где расположены резервные копии приложения.
     * @return string
     */
    protected function storePath(string $path = null): string
    {
        return $this->config['store_path'].($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Получить базовую директорию приложения.
     * @return string
     */
    protected function basePath(string $path = null): string
    {
        return base_path($path);
    }

    /**
     * .
     * @return array
     */
    protected function directories(string $type = null): array
    {
        return empty($type)
            ? $this->config['directories']
            : $this->config['directories'][$type];
    }

    /**
     * .
     * @return array
     */
    protected function symlinks(): array
    {
        return $this->config['symlinks'];
    }

    /**
     * .
     * @return array
     */
    protected function allowedFiles(): array
    {
        return $this->config['allowed_files'];
    }

    /**
     * Массовое задание параметров архивирования / восстановления.
     * @param  array  $options
     * @return self
     */
    public function setOptions(array $options): ArchivistContract
    {
        foreach ($options as $option) {
            $this->with($option);
        }

        return $this;
    }

    /**
     * Установить операции, которые должны будут выполнены.
     * @param  mixed  $options
     * @return self
     */
    public function with($options): ArchivistContract
    {
        $options = is_string($options) ? func_get_args() : $options;

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Убрать операции из списка запланированных.
     * @param  mixed  $options
     * @return self
     */
    public function without($options): ArchivistContract
    {
        $options = is_string($options) ? func_get_args() : $options;
        
        $this->options = array_diff($this->options, $options);

        return $this;
    }

    /**
     * Запустить архивирование / восстановление с запланированными операциями.
     * @param  array  $options
     * @return mixed
     */
    abstract public function execute();

    // /**
    //  * Получить массив папок, игнорируемых во время процесса обновления.
    //  * @return array
    //  */
    // abstract protected function excludeDirectories(): array;
    //
    // /**
    //  * Получить массив файлов, которые расположены
    //  * в корне приложения и будут обновлены.
    //  * @return array
    //  */
    // abstract protected function allowedFiles(): array;
    //
    // /**
    //  * Корневая директория обновляемого приложения.
    //  * @return string
    //  */
    // abstract protected function destinationPath(): string;
    //
    // /**
    //  * Получить временную директорию, где расположены
    //  * исходники файлов обновляемого приложения.
    //  * @return string
    //  */
    // abstract protected function sourcePath(): string;

    /**
     * Получить коллекцию файлов резервных копий,
     * включая их свойства: имя, размер, дата создания.
     * @return object
     */
    public function backups(): object
    {
        $files = $this->filesystem->allFiles($this->storePath());

        return collect($files)
            ->filter(function (SplFileInfo $file, int $index) {
                return $file->isReadable()
                    && $file->getExtension() === Archivist::FILE_EXTENSION_BACKUP;
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
    public function deleteBackup(string $filename): bool
    {

    }

    /**
     * Удалить все файлы резервных копий.
     * @return bool
     */
    public function deleteAllBackups(): bool
    {

    }

    public static function formatSize($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $i).' '.$units[$i];
    }

    /**
     * Рекурсивное удаление директорий из директории исходника,
     * исключаемых из процесса обновления согласно конфигурации.
     * @param  string  $sourcePath
     * @return void
     */
    protected function deleteExcludeDirectories(string $sourcePath): void
    {
        $toRemoved = Finder::create()->in($sourcePath)->directories()
            ->path($this->excludeDirectories())->sortByName();

        collect($toRemoved)->each(function (SplFileInfo $directory) {
            File::deleteDirectory($directory->getRealPath());
        });
    }

    /**
     * Рекурсивное копирование содержимого директории исходников.
     * @param  string  $sourcePath
     * @param  string  $destinationPath
     * @return void
     *
     * @NB Нельзя разбивать данный метод на несколько, так как
     * после обновления директорий данный будет перезаписан
     * и может быть выброшено исключение об отсутствии какого-либо метода,
     * например по копированию корневых файлов:
     * `Call to undefined function`.
     *
     * @NB Остаётся нерешенной проблема, когда вендор-пакеты содержат
     * скрытые директории `.git`. `Finder` и в этом случае
     * ведет себя не понятно, так как он должен их игнорировать.
     */
    protected function copySourceDirectory(string $sourcePath, string $destinationPath): void
    {
        // // 1. Предварительно выполняем проверку файлов на перезапись.
        // $this->assertFilesInDirectoryIsReadable($sourcePath);

        // 2. Удаляем принудительно директории, так как
        //    метод `exclude` класса Finder непонятно работает.
        $this->deleteExcludeDirectories($sourcePath);

        // 3. Рекурсивное копирование директорий с содержимым
        //    из директории исходника в корневую директорию приложения.
        $directories = Finder::create()->in($sourcePath)->directories()
            ->sortByName();

        collect($directories)->each(function (SplFileInfo $directory) use ($destinationPath) {
            $destinationPath .= DIRECTORY_SEPARATOR.$directory->getRelativePath();

            File::copyDirectory(
                $directory->getRealPath(),
                $destinationPath.DIRECTORY_SEPARATOR.$directory->getBasename()
            );
        });

        // 4. Рекурсивное копирование корневых файлов из директории исходника
        //    в корневую директорию приложения согласно конфигурации.
        $files = Finder::create()->in($sourcePath)->files()
            ->depth(0)->ignoreDotFiles(true)
            ->name($this->allowedFiles())->sortByName();

        collect($files)->each(function (SplFileInfo $file) use ($destinationPath) {
            File::copy(
                $file->getRealPath(),
                $destinationPath.DIRECTORY_SEPARATOR.$file->getFilename()
            );
        });

        // 5. Удаляем временную директорию с исходниками.
        File::deleteDirectory($sourcePath);
    }

    /**
     * Рекурсивная проверка файлов по указанному пути на доступность для записи.
     * @param  string  $destinationPath
     * @return bool
     *
     * @throws RuntimeException
     *
     * @NB Не проверяет скрытые директории и `.git`.
     */
    protected function assertFilesInDirectoryIsWritable(string $destinationPath): bool
    {
        $files = Finder::create()->in($destinationPath)->files()
            ->exclude($this->excludeDirectories());

        $firstLockedFile = collect($files)->first(function (SplFileInfo $file, $key) {
            return ! $file->isWritable();
        }, null);

        if (is_null($firstLockedFile)) {
            return true;
        }

        throw new RuntimeException(sprintf(
            'Файл [%s] не доступен для записи.',
            $firstLockedFile->getRelativePath().DIRECTORY_SEPARATOR.$firstLockedFile->getFilename()
        ));
        // $firstLockedFile->getRelativePathname()
    }
}

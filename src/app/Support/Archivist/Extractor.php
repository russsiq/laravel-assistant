<?php

namespace Russsiq\Assistant\Support\Archivist;

// Исключения.
use InvalidArgumentException;

// Базовые расширения PHP.
use SplFileInfo;

// Зарегистрированные фасады приложения.
use Russsiq\Assistant\Facades\Updater;

// Сторонние зависимости.
use Russsiq\Assistant\Contracts\ArchivistContract;
use Russsiq\Assistant\Contracts\Archivist\CanRestore;
use Russsiq\Assistant\Support\Archivist\AbstractArchivist;

/**
 * Экземпляр Распаковщика.
 */
class Extractor extends AbstractArchivist implements CanRestore
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
        if (empty($options['restore'])) {
            throw new InvalidArgumentException(
                "Action [restore] is not defined."
            );
        }

        if (isset($options['filename'])) {
            $this->from($options['filename']);
        }

        return parent::setOptions($options['restore']);
    }

    /**
     * Запустить архивирование / восстановление.
     * @return mixed
     */
    public function execute()
    {
        return $this->restore($this->filename, $this->options);
    }

    /**
     * Задать рабочий файл резервной копии.
     * В текущем методе только задаем имя файла.
     * Проверку существования файла оставляем на метод `restore`.
     * @param  string  $filename
     * @return self
     */
    public function from(string $filename): CanRestore
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Восстановить резервную копию в соответствии с выбранными опциями.
     * @return void
     */
    public function restore()
    {
        // $filename = $this->storePath($this->filename);
        //
        // if ($this->filesystem->isFile($filename)) {
        //     $this->filename = $filename;
        // }
        //
        // $this->unzipArchive($filename, $this->storePath('tmp'));

        dd('restore after execute', $this->filename, $options);
    }

    /**
     * Извлечь архив с исходниками для последующего обновления.
     * @param  string  $filename
     * @param  string  $destination
     * @return bool
     */
    public function unzipArchive(string $filename, string $destination): bool
    {
        @ini_set('max_execution_time', 120);

        try {
            $opened = $this->ziparchive->open($filename);
            $this->assertZiparchiveIsOpened($filename, $opened);

            $extracted = $this->ziparchive->extractTo($destination);
            $this->assertZiparchiveIsExtracted($filename, $extracted);

            $this->ziparchive->close();

            $this->ensureSourceInRootDirectory($destination);

            return true;
        } catch (Throwable $e) {
            // $this->filesystem->delete($filename);

            throw $e;
        }
    }

    /**
     * Убедиться, что извлеченные файлы не имеют
     * посторонней вложенной директории,
     * т.е. исходники расположены в корневой директории.
     * @param  string  $destination
     * @return void
     */
    protected function ensureSourceInRootDirectory(string $destination)
    {
        $directories = $this->filesystem->directories($destination);

        if (1 === count($directories)) {
            $root = $directories[0];

            collect($this->filesystem->directories($root))
                ->each(function (string $directory) use ($destination) {
                    $this->filesystem->moveDirectory(
                        $directory,
                        $destination.DIRECTORY_SEPARATOR.$this->filesystem->name($directory)
                    );
                });

            collect($this->filesystem->files($root, true))
                ->each(function (SplFileInfo $file) use ($destination) {
                    $this->filesystem->move(
                        $file->getRealPath(),
                        $destination.DIRECTORY_SEPARATOR.$file->getFilename()
                    );
                });

            $this->filesystem->deleteDirectory($root);
        }
    }

    /**
     * Определить, произошла ли ошибка во время открытия архива.
     *
     * @param  string  $filename
     * @param  mixed  $opened
     * @return void
     *
     * @throws RuntimeException
     */
    protected function assertZiparchiveIsOpened(string $filename, $opened)
    {
        if ($opened !== true) {
            throw new RuntimeException(sprintf(
                "Cannot open zip archive [%s].",
                $filename
            ));
        }
    }

    /**
     * Определить, произошла ли ошибка во время извлечения файлов из архива.
     *
     * @param  string  $filename
     * @param  mixed  $extracted
     * @return void
     *
     * @throws RuntimeException
     */
    protected function assertZiparchiveIsExtracted(string $filename, $extracted)
    {
        if ($extracted !== true) {
            throw new RuntimeException(sprintf(
                "Unable to extract zip archive [%s].",
                $filename
            ));
        }
    }
}

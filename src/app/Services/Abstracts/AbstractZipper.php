<?php

namespace Russsiq\Assistant\Services\Abstracts;

// Базовые расширения PHP.
use SplFileInfo;
use ZipArchive;

// Сторонние зависимости.
use Illuminate\Filesystem\Filesystem;
use Russsiq\Assistant\Services\Contracts\ZipperContract;

/**
 * Абстрактный класс-обертка для архиватора.
 */
abstract class AbstractZipper implements ZipperContract
{
    /**
     * Экземпляр класса по работе с файловой системой.
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Экземпляр класса по работе с архивами.
     * @var ZipArchive
     */
    protected $ziparchive;

    /**
     * Создать новый экземпляр класса.
     * @param  Filesystem  $filesystem
     * @param  ZipArchive  $ziparchive
     */
    public function __construct(
        Filesystem $filesystem,
        ZipArchive $ziparchive
    ) {
        $this->filesystem = $filesystem;
        $this->ziparchive = $ziparchive;
    }

    /**
     * Получить полный путь, включая имя, текущего рабочего архива.
     * @return string|null
     */
    abstract public function filename(): ?string;

    /**
     * Открыть архив для последующей работы с ним
     * (для чтения, записи или изменения).
     * @param  string  $filename
     * @param  mixed  $flags
     * @return self
     */
    abstract public function open(string $filename, $flags = null): ZipperContract;

    /**
     * Создать архив для последующей работы с ним
     * (для чтения, записи или изменения).
     * @param  string  $filename
     * @param  mixed  $flags
     * @return self
     */
    abstract public function create(string $filename, $flags = null): ZipperContract;

    /**
     * Извлечь весь архив или его части в указанное место назначения.
     * @param  string  $destination  Место назначение, куда извлекать файлы.
     * @param  array|null  $entries  Массив элементов для извлечения.
     * @return bool
     */
    abstract public function extractTo(string $destination, array $entries = null): bool;

    /**
     * Добавить в архив файл по указанному пути.
     * @param  string  $filename
     * @param  string|null  $localname
     * @return bool
     */
    abstract public function addFile(string $filename, string $localname = null) : bool;

    /**
     * Добавить в архив директорию.
     * @param  string  $realPath
     * @param  string  $relativePath
     * @param  integer  $flags
     * @return bool
     */
    abstract public function addDirectory(string $realPath, string $relativePath): bool;

    /**
     * Добавить в архив пустую директорию.
     * @param  string  $dirname
     * @param  integer  $flags
     * @return bool
     */
    abstract public function addEmptyDirectory(string $dirname): bool;

    /**
     * Удалить элемент (файл) в архиве, используя его имя.
     * @param  string  $filename
     * @return bool
     */
    abstract public function deleteFile(string $filename): bool;

    /**
     * Удалить элемент (каталог) в архиве, используя его имя.
     * @param  string  $dirname
     * @return bool
     */
    abstract public function deleteDirectory(string $dirname): bool;

    /**
     * Закрыть текущий (открытый или созданный) архив и сохранить изменения.
     * @return bool
     */
    abstract public function close(): bool;

    /**
     * Убедиться, что извлеченные файлы не имеют
     * посторонней вложенной директории,
     * т.е. исходники расположены в корневой директории.
     *
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

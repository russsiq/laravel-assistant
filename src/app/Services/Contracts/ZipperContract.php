<?php

namespace Russsiq\Assistant\Services\Contracts;

//
use ZipArchive;

// Сторонние зависимости.

/**
 * Контракт публичных методов ...
 * @var interface
 */
interface ZipperContract
{
    /**
     * Получить полный путь, включая имя, текущего рабочего архива.
     * @return string|null
     */
    public function path(): ?string;

    /**
     * Получить имя текущего рабочего архива без информации о пути к нему.
     * @return string
     */
    public function name(): ?string;

    /**
     * Открыть архив для последующей работы с ним
     * (для чтения, записи или изменения).
     * @param  string  $filename
     * @param  mixed  $flags
     * @return self
     */
    public function open(string $filename, $flags = null): self;

    /**
     * Создать архив для последующей работы с ним
     * (для чтения, записи или изменения).
     * @param  string  $filename
     * @param  mixed  $flags
     * @return self
     */
    public function create(string $filename, $flags = null): self;

    /**
     * Извлечь весь архив или его части в указанное место назначения.
     * @param  string  $destination  Место назначение, куда извлекать файлы.
     * @param  array|null  $entries  Массив элементов для извлечения.
     * @return bool
     */
    public function extractTo(string $destination, array $entries = null): bool;

    /**
     * Добавить в архив файл по указанному пути.
     * @param  string  $filename
     * @param  string|null  $localname
     * @return bool
     */
    public function addFile(string $filename, string $localname = null) : bool;

    /**
     * Добавить в архив директорию.
     * @param  string  $realPath
     * @param  string  $relativePath
     * @param  integer  $flags
     * @return bool
     */
    public function addDirectory(string $realPath, string $relativePath): bool;

    /**
     * Добавить в архив пустую директорию.
     * @param  string  $dirname
     * @param  integer  $flags
     * @return bool
     */
    public function addEmptyDirectory(string $dirname): bool;

    /**
     * Удалить элемент (файл) в архиве, используя его имя.
     * @param  string  $filename
     * @return bool
     */
    public function deleteFile(string $filename): bool;

    /**
     * Удалить элемент (каталог) в архиве, используя его имя.
     * @param  string  $dirname
     * @return bool
     */
    public function deleteDirectory(string $dirname): bool;

    /**
     * Закрыть текущий (открытый или созданный) архив и сохранить изменения.
     * @return bool
     */
    public function close(): bool;
}

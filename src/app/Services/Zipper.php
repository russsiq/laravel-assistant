<?php

namespace Russsiq\Assistant\Services;

// Базовые расширения PHP.
use SplFileInfo;
use ZipArchive;

// Сторонние зависимости.
use Russsiq\Assistant\Services\Abstracts\AbstractZipper;
use Russsiq\Assistant\Services\Contracts\ZipperContract;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Str;

/**
 * Класс-обертка для архиватора ZipArchive.
 */
class Zipper extends AbstractZipper
{
    /**
     * Получить количество файлов в архиве.
     * @return int
     */
    public function count(): int
    {
        return $this->ziparchive->numFiles;
    }

    /**
     * Получить полный путь, включая имя, текущего рабочего архива.
     * @return string|null
     */
    public function filename(): ?string
    {
        return $this->ziparchive->filename ?: null;
    }

    /**
     * Открыть архив для последующей работы с ним
     * (для чтения, записи или изменения).
     * @param  string  $filename
     * @param  mixed  $flags
     * @return self
     */
    public function open(string $filename, $flags = null): ZipperContract
    {
        $this->ziparchive->open($filename);

        return $this;
    }

    /**
     * Создать архив для последующей работы с ним
     * (для чтения, записи или изменения).
     * @param  string  $filename
     * @param  mixed  $flags
     * @return self
     */
    public function create(string $filename, $flags = null): ZipperContract
    {
        $this->ziparchive->open($filename, ZipArchive::CREATE);

        return $this;
    }

    /**
     * Извлечь весь архив или его части в указанное место назначения.
     * @param  string  $destination  Место назначение, куда извлекать файлы.
     * @param  array|null  $entries  Массив элементов для извлечения.
     * @return bool
     */
    public function extractTo(string $destination, array $entries = null): bool
    {
        return $this->ziparchive->extractTo($destination);
    }

    /**
     * Добавить в архив файл по указанному пути.
     * @param  string  $filename  Абсолютный путь добавляемого файла.
     * @param  string|null  $localname  Относительный путь к файлу в архиве, включая его имя.
     * @return bool
     */
    public function addFile(string $filename, string $localname = null) : bool
    {
        return $this->ziparchive->addFile($filename, $localname);
    }

    /**
     * Добавить в архив директорию.
     * @param  string  $realPath
     * @param  string  $relativePath
     * @param  integer  $flags
     * @return bool
     */
    public function addDirectory(string $realPath, string $relativePath): bool
    {
        $finder = $this->createFinder($realPath)
            ->ignoreDotFiles(false)
            ->ignoreVCS(false);

        // Исключаем файлы из списка архивируемых,
        // доступ к которым возможен через ссылки.
        $exluded = [];

        foreach ($finder->getIterator() as $file) {
            // На Windows 7 некорректная работа со считыванием информации о ссылках.
            if ($file->isLink() or (! in_array($file->getType(), ['file', 'dir']))) {
                // А вот и причина ошибки, из-за которой метод `exclude` неверно отрабатывал.
                // https://github.com/symfony/finder/blob/008b6cc6da7141baf1766d72d2731b0e6f78b45b/Iterator/ExcludeDirectoryFilterIterator.php#L37
                // https://github.com/russsiq/laravel-assistant/blob/7b36b4a1a56c4364b470db3188c06eb1871ed8f7/src/app/Support/Updater/AbstractUpdater.php#L166
                $exluded[] = str_replace('\\', '/', $file->getRelativePathname());
            }
        }

        $files = $finder->exclude($exluded)->files();

        foreach ($files as $file) {
            $this->addFile(
                $file->getRealPath(),
                $relativePath.DIRECTORY_SEPARATOR.$file->getRelativePathname()
            );
        }

        return true;
    }

    /**
     * Добавить в архив пустую директорию.
     * @param  string  $dirname
     * @param  integer  $flags
     * @return bool
     */
    public function addEmptyDirectory(string $dirname): bool
    {
        return $this->ziparchive->addEmptyDir($dirname);
    }

    /**
     * Удалить элемент (файл) в архиве, используя его имя.
     * @param  string  $filename
     * @return bool
     */
    public function deleteFile(string $filename): bool
    {
        return $this->ziparchive->deleteName($filename);
    }

    /**
     * Удалить элемент (каталог) в архиве, используя его имя.
     * @param  string  $dirname
     * @return bool
     */
    public function deleteDirectory(string $dirname, $force = false): bool
    {
        // Архиватор обрабатывает директории, которые оканчиваются на `/`.
        $dirname = rtrim($dirname, '/\\');
        $dirname = $dirname . '/';

        $result = $this->ziparchive->deleteName($dirname);

        // dd($result);

        return $result;
    }

    /**
     * Закрыть текущий (открытый или созданный) архив и сохранить изменения.
     * @return bool
     */
    public function close(): bool
    {
        return $this->ziparchive->close();
    }

    /**
     * Создать экземпляр Поисковика.
     * @param  mixed  $directories
     * @return Finder
     */
    protected function createFinder($directories): Finder
    {
        return Finder::create()->in((array) $directories);
    }
}

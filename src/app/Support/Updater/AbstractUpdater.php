<?php

namespace Russsiq\Assistant\Support\Updater;

use Exception;
use InvalidArgumentException;
use RuntimeException;

use EnvManager;

use Russsiq\Assistant\Contracts\UpdaterContract;

use File;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

abstract class AbstractUpdater implements UpdaterContract
{
    /**
     * Получить дату установки приложения.
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function installedAt()
    {
        if ($time = EnvManager::get('APP_INSTALLED_AT')) {
            return $time;
        }

        throw new RuntimeException(
            'Не указана дата установки приложения.'
        );
    }

    /**
     * Получить номер фактической версии приложения.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function currentlyVersion(): string
    {
        if ($version = EnvManager::get('APP_VERSION')) {
            return $version;
        }

        throw new RuntimeException(
            'Не указана текущая версия приложения.'
        );
    }

    /**
     * Получить номер доступной версии приложения,
     * опубликованного в репозитории.
     *
     * @return string
     */
    abstract public function availableVersion(): string;

    /**
     * Доступность новой версии приложения в репозитории.
     *
     * @return bool
     */
    public function isNewVersionAvailable(): bool
    {
        return version_compare(
            $this->currentlyVersion(),
            $this->availableVersion(),
            '<'
        );
    }

    /**
     * Получить массив папок, игнорируемых во время процесса обновления.
     *
     * @return array
     */
    abstract protected function excludeDirectories(): array;

    /**
     * Получить массив файлов, которые расположены
     * в корне приложения и будут обновлены.
     *
     * @return array
     */
    abstract protected function allowedFiles(): array;

    /**
     * Корневая директория обновляемого приложения.
     *
     * @return string
     */
    abstract protected function destinationPath(): string;

    /**
     * Получить временную директорию, где расположены
     * исходники файлов обновляемого приложения.
     *
     * @return string
     */
    abstract protected function sourcePath(): string;

    /**
     * Загрузить архив новой версии приложения
     * из репозитория с помощью НТТР-метода GЕТ.
     *
     * @return void
     */
    abstract public function fetch();

    /**
     * Запустить процесс обновления приложения до актуальной версии.
     *
     * @return bool
     */
    abstract public function update(): bool;

    /**
     * Рекурсивное удаление директорий из директории исходника,
     * исключаемых из процесса обновления согласно конфигурации.
     *
     * @param  string  $sourcePath
     * @return void
     */
    protected function deleteExcludeDirectories(string $sourcePath)
    {
        $toRemoved = Finder::create()->in($sourcePath)->directories()
            ->path($this->excludeDirectories())->sortByName();

        collect($toRemoved)->each(function (SplFileInfo $directory) {
            File::deleteDirectory($directory->getRealPath());
        });
    }

    /**
     * Рекурсивное перемещение директорий с содержимым
     * из директории исходника в корневую директорию приложения.
     *
     * @param  string  $sourcePath
     * @param  string  $destinationPath
     * @return void
     */
    protected function moveSourceDirectories(string $sourcePath, string $destinationPath)
    {
        $directories = Finder::create()->in($sourcePath)->directories()
            ->sortByName();

        collect($directories)->each(function (SplFileInfo $directory) use ($destinationPath) {
            $destinationPath .= DIRECTORY_SEPARATOR.$directory->getRelativePath();

            File::moveDirectory(
                $directory->getRealPath(),
                $destinationPath.DIRECTORY_SEPARATOR.$directory->getBasename()
            );
        });
    }

    /**
     * Рекурсивное перемещение корневых файлов из директории исходника
     * в корневую директорию приложения согласно конфигурации.
     *
     * @param  string  $sourcePath
     * @param  string  $destinationPath
     * @return void
     */
    protected function moveSourceRootFiles(string $sourcePath, string $destinationPath)
    {
        $files = Finder::create()->in($sourcePath)->files()
            ->depth(0)->ignoreDotFiles(true)
            ->name($this->allowedFiles())->sortByName();

        collect($files)->each(function (SplFileInfo $file) use ($destinationPath) {
            File::move(
                $file->getRealPath(),
                $destinationPath.DIRECTORY_SEPARATOR.$file->getFilename()
            );
        });
    }

    /**
     * Удаление временной директории с исходниками.
     *
     * @param  string  $sourcePath
     * @return bool
     */
    protected function deleteSourceDirectory(string $sourcePath): bool
    {
        return File::deleteDirectory($sourcePath);
    }
}

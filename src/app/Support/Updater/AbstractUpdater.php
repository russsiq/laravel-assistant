<?php

namespace Russsiq\Assistant\Support\Updater;

// Исключения.
use Exception;
use InvalidArgumentException;
use RuntimeException;

// Базовые расширения PHP.
use SplFileInfo;

// Зарегистрированные фасады приложения.
use EnvManager;
use File;

// Сторонние зависимости.
use Russsiq\Assistant\Contracts\UpdaterContract;
use Symfony\Component\Finder\Finder;

/**
 * Абстрактная реализация Мастера обновлений.
 */
abstract class AbstractUpdater implements UpdaterContract
{
    /**
     * Получить дату установки приложения.
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
     * @return string
     */
    abstract public function availableVersion(): string;

    /**
     * Доступность новой версии приложения в репозитории.
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
     * @return array
     */
    abstract protected function excludeDirectories(): array;

    /**
     * Получить массив файлов, которые расположены
     * в корне приложения и будут обновлены.
     * @return array
     */
    abstract protected function allowedFiles(): array;

    /**
     * Корневая директория обновляемого приложения.
     * @return string
     */
    abstract protected function destinationPath(): string;

    /**
     * Получить временную директорию, где расположены
     * исходники файлов обновляемого приложения.
     * @return string
     */
    abstract protected function sourcePath(): string;

    /**
     * Загрузить архив новой версии приложения
     * из репозитория с помощью НТТР-метода GЕТ.
     * @return void
     */
    abstract public function fetch();

    /**
     * Запустить процесс обновления приложения до актуальной версии.
     * @return bool
     */
    abstract public function update(): bool;

    /**
     * Обновить номер текущей версии приложения.
     * @return bool
     */
    protected function updateCurrentlyVersion(): bool
    {
        return EnvManager::set('APP_VERSION', $this->availableVersion())
            ->save();
    }

    /**
     * Рекурсивная проверка файлов по указанному пути на доступность для записи.
     * @param  string  $destinationPath
     * @return bool
     *
     * @throws RuntimeException
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
    }

    /**
     * Рекурсивное удаление директорий из директории исходника,
     * исключаемых из процесса обновления согласно конфигурации.
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
}

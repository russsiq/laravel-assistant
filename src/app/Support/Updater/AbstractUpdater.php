<?php

namespace Russsiq\Assistant\Support\Updater;

use EnvManager;

use Russsiq\Assistant\Contracts\UpdaterContract;

abstract class AbstractUpdater implements UpdaterContract
{
    /**
     * Получить дату установки приложения.
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function installedAt()
    {
        if ($time = EnvManager::get('APP_INSTALLED_AT')) {
            return $time;
        }

        throw new InvalidArgumentException(
            'Не указана дата установки приложения.'
        );
    }

    /**
     * Получить номер фактической версии приложения.
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function currentlyVersion(): string
    {
        if ($version = EnvManager::get('APP_VERSION')) {
            return $version;
        }

        throw new InvalidArgumentException(
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
     * Получить массив файлов, расположеных
     * в корне приложения, которые будут обновлены.
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
}

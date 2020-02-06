<?php

namespace Russsiq\Assistant\Support\Updater;

use Russsiq\Assistant\Contracts\UpdaterContract;

abstract class AbstractUpdater implements UpdaterContract
{
    /**
     * Получить дату установки приложения.
     *
     * @return mixed
     */
    public function installedAt()
    {
        //
    }

    /**
     * Получить номер фактической версии приложения.
     *
     * @return string
     */
    public function currentlyVersion(): string
    {
        //
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
        //
    }

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

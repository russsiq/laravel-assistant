<?php

namespace Russsiq\Assistant\Support\Updater;

use Illuminate\Foundation\Application;

use Russsiq\Assistant\Contracts\UpdaterContract;

class UpdaterManager implements UpdaterContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр Мастера обновлений.
     *
     * @param  Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        dump('UPDATER');
    }

    /**
     * Получить дату установки приложения.
     *
     * @return mixed
     */
    public function installedAt()
    {
        // code...
    }

    /**
     * Получить номер фактической версии приложения.
     *
     * @return string
     */
    public function currentlyVersion(): string
    {
        // code...
    }

    /**
     * Получить номер доступной версии приложения,
     * опубликованного в репозитории.
     *
     * @return string
     */
    public function availableVersion(): string
    {
        // code...
    }

    /**
     * Доступность новой версии приложения в репозитории.
     *
     * @return bool
     */
    public function isNewVersionAvailable(): bool
    {
        // code...
    }

    /**
     * Загрузить архив новой версии приложения
     * из репозитория с помощью НТТР-метода GЕТ.
     *
     * @return void
     */
    public function fetch()
    {
        // code...
    }

    /**
     * Запустить процесс обновления приложения до актуальной версии.
     *
     * @return bool
     */
    public function update(): bool
    {
        // code...
    }
}

<?php

namespace Russsiq\Assistant\Support;

use Artisan;
use EnvManager;

use Illuminate\Foundation\Application;

use Russsiq\Assistant\Support\Contracts\InstallerContract;

class Installer implements InstallerContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр Установщика приложения.
     *
     * @param  Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Инициировать начальный этап установки.
     *
     * @return void
     */
    public function initiate()
    {
        // Создаем новый файл из образца,
        // попутно генерируя ключ для приложения.
        EnvManager::newFromPath(base_path('.env.example'), true)
            // Устанавливаем необходимые значения.
            ->setMany([
                'APP_URL' => url('/'),
            ])
            // Сохраняем новый файл в корне как `.env`.
            ->save();

        // Очищаем ненужный хлам.
        $exit_code = Artisan::call('cache:clear');
        $exit_code = Artisan::call('config:clear');
        $exit_code = Artisan::call('route:clear');
        $exit_code = Artisan::call('view:clear');

        // Для запуска приложения необходимо задать минимальные параметры.
        config([
            'app.key' => EnvManager::get('APP_KEY')
        ]);
    }

    /**
     * Маркер того, что была выполнена
     * первоначальная инициализация установки.
     *
     * @return boolean
     */
    public function alreadyInitiated(): bool
    {
        return EnvManager::fileExists();
    }

    /**
     * Маркер, что приложение установлено.
     *
     * @return boolean
     */
    public function alreadyInstalled(): bool
    {
        return (bool) $this->installedAt();
    }

    /**
     * Получить дату установки приложения.
     *
     * @return mixed
     */
    public function installedAt()
    {
        return strtotime(EnvManager::get('APP_INSTALLED_AT'));
    }

    /**
     * Получить массив с набором минимальных системных требований к серверу.
     *
     * @return array
     */
    public static function requirements(): array
    {
        return server_requirements();
    }

    /**
     * Получить массив "зловредных" глобальных переменных.
     *
     * @return array
     */
    public static function antiGlobals(): array
    {
        return anti_globals();
    }

    /**
     * Получить массив прав на доступ к директориям.
     *
     * @return array
     */
    public static function filePermissions(): array
    {
        return file_permissions();
    }

    /**
     * Получить массив доступных при установке тем.
     *
     * @return array
     */
    public static function themes(): array
    {
        // code...
    }

    /**
     * Выполнить проверку подключения к БД с переданными параметрами.
     *
     * @return void
     */
    public function checkConnection(array $params, string $connection)
    {
        // code...
    }

    /**
     * Выполнить миграции БД.
     *
     * @return string   Сообщение о выполненной операции.
     */
    public function migrate(): string
    {
        // code...
    }

    /**
     * Заполнить БД фиктивными данными.
     *
     * @return string   Сообщение о выполненной операции.
     */
    public function seed(): string
    {
        // code...
    }
}

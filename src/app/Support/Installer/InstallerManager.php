<?php

namespace Russsiq\Assistant\Support\Installer;

use Artisan;
use DB;
use EnvManager;
use SplFileInfo;

use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Russsiq\Assistant\Contracts\InstallerContract;
use Russsiq\Assistant\Exceptions\InstallerFailed;
use Russsiq\Assistant\Services\Abstracts\AbstractBeforeInstalled;

use Symfony\Component\Finder\Finder;

class InstallerManager implements InstallerContract
{
    /**
     * Расположение класса финальной стадии Установщика.
     *
     * @var string
     */
    const DEFAULT_BEFORE_INSTALLED = '\Russsiq\Assistant\Services\BeforeInstalled';

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
     *
     * @throws InstallerFailed
     */
    public function checkConnection(array $params, string $connection = 'mysql')
    {
        // Set temporary DB connection
        $config = config("database.connections.$connection");

        config([
            "database.connections.$connection" => array_merge($config, [
                'host' =>  $params['DB_HOST'],
                'database' => $params['DB_DATABASE'],
                'prefix' => $params['DB_PREFIX'],
                'username' => $params['DB_USERNAME'],
                'password' => $params['DB_PASSWORD'],
            ]),
        ]);

        // Check DB connection and exists table
        DB::purge($connection);
        DB::reconnect($connection);
        DB::setTablePrefix($params['DB_PREFIX']);
        DB::connection()->getPdo();

        if (is_null(DB::connection($connection)->getDatabaseName())) {
            throw new InstallerFailed(__('msg.not_dbconnect'));
        }
    }

    /**
     * Выполнить миграции БД.
     *
     * @return string   Сообщение о выполненной операции.
     */
    public function migrate(): string
    {
        try {
            // Запускаем запись транзакции.
            DB::beginTransaction();

            // Выполняем миграции через Artisan.
            Artisan::call('migrate', [
                '--force' => true,

            ]);

            // После коммита текущая транзакция минусуется.
            DB::commit();
        } catch (\Throwable $e) {

            throw $e;
        } finally {
            // Откат применяется только к текущей транзакции.
            // После коммита нечего откатывать.
            // Если выброшено исключение до коммита,
            // то будет выполнен откат транзакции.
            DB::rollback();
        }

        return Artisan::output();
    }

    /**
     * Заполнить БД данными.
     *
     * @param  string $class Класс заполнителя.
     *
     * @return string        Сообщение о выполненной операции.
     */
    public function seed(string $class): string
    {
        try {
            // Запускаем запись транзакции.
            DB::beginTransaction();

            // Заполняем БД данными при помощи Artisan.
            Artisan::call('db:seed', [
                '--force' => true,
                '--class' => $class,

            ]);

            // После коммита текущая транзакция минусуется.
            DB::commit();
        } catch (\Throwable $e) {

            throw $e;
        } finally {
            // Откат применяется только к текущей транзакции.
            // После коммита нечего откатывать.
            // Если выброшено исключение до коммита,
            // то будет выполнен откат транзакции.
            DB::rollback();
        }

        return Artisan::output();
    }

    /**
     * [beforeInstalled description]
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function beforeInstalled(Request $request): RedirectResponse
    {
        $provider = $this->createBeforeInstalled(
            config('assistant.installer.before-installed', self::DEFAULT_BEFORE_INSTALLED)
        );

        return $provider->handle($request);
    }

    protected function createBeforeInstalled(string $class): AbstractBeforeInstalled
    {
        return new $class($this->app);
    }

    public function copyDirectories()
    {
        $directories = config('assistant.installer.directories');

        if (is_array($directories) and count($directories)) {
            $filesystem = $this->app->make('files');

            foreach ($directories as $fromDir => $toDir) {
                $this->copyDirectory($fromDir, $toDir, $filesystem);
            }
        }
    }

    public function copyDirectory(string $fromDir, string $toDir, $filesystem = null)
    {
        $filesystem = $filesystem ?: $this->app->make('files');

        collect(Finder::create()->directories()->in($fromDir)->sortByName())
            ->each(function (SplFileInfo $directory) use ($toDir, $filesystem) {
                $filesystem->copyDirectory(
                    $directory->getRealPath(),
                    $toDir.DS.$directory->getRelativePath().DS.$directory->getBasename()
                );
            });

        collect(Finder::create()->files()->in($fromDir)->depth(0)->ignoreDotFiles(true)->sortByName())
            ->each(function (SplFileInfo $file) use ($toDir, $filesystem) {
                $filesystem->copy(
                    $file->getRealPath(),
                    $toDir.DS.$file->getFilename()
                );
            });
    }

    // Artisan::call('storage:link');
    public function createSymbolicLinks()
    {
        $symlinks = config('assistant.installer.symlinks');

        if (is_array($symlinks) and count($symlinks)) {
            $filesystem = $this->app->make('files');

            foreach ($symlinks as $target => $link) {
                clearstatcache(true, $link);

                if (! $filesystem->exists($link)) {
                    $filesystem->link($target, $link);
                }
            }
        }
    }

    /**
     * Применить замыкание, если переданное условие `$condition` правдиво.
     *
     * @param  bool  $condition
     * @param  callable  $callback
     *
     * @return self
     */
    public function when(bool $condition, callable $callback): InstallerContract
    {
        if ($condition) {
            $callback($this, $condition);
        }

        return $this;
    }
}
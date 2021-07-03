<?php

namespace Russsiq\Assistant\Support\Installer;

use Illuminate\Contracts\Config\Repository as ConfigRepositoryContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Russsiq\Assistant\Contracts\InstallerContract;
use Russsiq\Assistant\Exceptions\InstallerFailed;
use Russsiq\Assistant\Facades\Cleaner;
use Russsiq\Assistant\Services\Abstracts\AbstractBeforeInstalled;
use Russsiq\EnvManager\Facades\EnvManager;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Throwable;

/**
 * Установщик.
 */
class InstallerManager implements InstallerContract
{
    /**
     * Расположение класса финальной стадии Установщика.
     *
     * @var string
     */
    const DEFAULT_BEFORE_INSTALLED = '\Russsiq\Assistant\Services\BeforeInstalled';

    /**
     * Экземпляр контейнера приложения.
     */
    protected Container $container;

    /**
     * Экземпляр репозитория конфигураций.
     */
    protected ConfigRepositoryContract $config;

    /**
     * Экземпляр класса по работе с файловой системой.
     */
    protected Filesystem $filesystem;

    /**
     * Создать новый экземпляр Установщика приложения.
     *
     * @param  Container  $container
     *
     * @return void
     */
    public function __construct(
        Container $container
    ) {
        $this->container = $container;
        $this->config = $container->make('config');
        $this->filesystem = $container->make('files');
    }

    /**
     * Инициировать начальный этап установки.
     *
     * @return void
     */
    public function initiate()
    {
        // Создаем новый файл из образца.
		EnvManager::newFromPath(base_path('.env.example'))
			// Попутно генерируем ключ для приложения.
			->withNewAppKey()
            // Устанавливаем необходимые значения.
            ->setMany([
                'APP_URL' => url('/'),
            ])
            // Сохраняем новый файл в корне как `.env`.
            ->save();

        // Очищаем ненужный хлам.
        Cleaner::process([
            'clear_cache',
            'clear_config',
            'clear_route',
            'clear_view',
        ]);

        // Для запуска приложения необходимо задать минимальные параметры.
        $this->config->set([
            'app.key' => EnvManager::get('APP_KEY'),
        ]);
    }

    /**
     * Маркер того, что была выполнена
     * первоначальная инициализация установки.
     *
     * @return bool
     */
    public function alreadyInitiated(): bool
    {
        return EnvManager::fileExists();
    }

    /**
     * Маркер, что приложение установлено.
     *
     * @return bool
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
     * Выполнить проверку подключения к БД с переданными параметрами.
     *
     * @return void
     *
     * @throws InstallerFailed
     */
    public function checkConnection(array $params, string $connection = 'mysql')
    {
        // Устанавливаем временное подключение к БД.
        $config = $this->config->get("database.connections.$connection");

        $this->config->set([
            "database.connections.$connection" => array_merge($config, [
                'host' => $params['DB_HOST'],
                'database' => $params['DB_DATABASE'],
                'prefix' => $params['DB_PREFIX'],
                'username' => $params['DB_USERNAME'],
                'password' => $params['DB_PASSWORD'],
            ]),
        ]);

        // Проверяем подключение к БД.
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
     * @return string  Сообщение о выполненной операции.
     */
    public function migrate(): string
    {
        try {
            // Выполняем миграции через Artisan.
            Artisan::call('migrate', [
                '--force' => true,

            ]);
        } catch (Throwable $e) {
            // В случае ошибки пытаемся откатить миграции.
            Artisan::call('migrate:reset', [

            ]);

            throw $e;
        }

        return Artisan::output();
    }

    /**
     * Заполнить БД данными.
     *
     * @param  string  $class  Класс заполнителя.
     *
     * @return string  Сообщение о выполненной операции.
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
        } catch (Throwable $e) {
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
     * Посредник, выполняющий заданные операции
     * на завершающей стадии установки приложения.
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function beforeInstalled(Request $request): RedirectResponse
    {
        $provider = $this->createBeforeInstalled(
            $this->config->get('assistant.installer.before-installed', self::DEFAULT_BEFORE_INSTALLED)
        );

        return $provider->handle($request);
    }

    /**
     * Создание посредника завершающей стадии.
     *
     * @param  string  $class
     *
     * @return AbstractBeforeInstalled
     */
    protected function createBeforeInstalled(string $class): AbstractBeforeInstalled
    {
        return new $class($this->container);
    }

    /**
     * Копирование директорий, заданных в массиве конфигурации.
     *
     * @return void
     */
    public function copyDirectories()
    {
        $directories = $this->config->get('assistant.installer.directories');

        if (is_array($directories) and count($directories)) {
            foreach ($directories as $fromDir => $toDir) {
                $this->copyDirectory($fromDir, $toDir);
            }
        }
    }

    /**
     * Копирование директории со всеми файлами.
     *
     * @param  string  $fromDir
     * @param  string  $toDir
     *
     * @return void
     */
    public function copyDirectory(string $fromDir, string $toDir)
    {
        collect(Finder::create()->directories()->in($fromDir)->sortByName())
            ->each(function (SplFileInfo $directory) use ($toDir) {
                $this->filesystem->copyDirectory(
                    $directory->getRealPath(),
                    $toDir.DIRECTORY_SEPARATOR.$directory->getRelativePath().DIRECTORY_SEPARATOR.$directory->getBasename()
                );
                // $this->filesystem->copyDirectory(
                //     $directory->getRealPath(),
                //     $toDir.DIRECTORY_SEPARATOR.$directory->getRelativePathname()
                // );
            });

        collect(Finder::create()->files()->in($fromDir)->depth(0)->ignoreDotFiles(true)->sortByName())
            ->each(function (SplFileInfo $file) use ($toDir) {
                $this->filesystem->copy(
                    $file->getRealPath(),
                    $toDir.DIRECTORY_SEPARATOR.$file->getFilename()
                );
            });
    }

    /**
     * Создание ссылок, заданных в массиве конфигурации.
     *
     * @return void
     */
    public function createSymbolicLinks()
    {
        $symlinks = $this->config->get('assistant.installer.symlinks');

        if (is_array($symlinks) and count($symlinks)) {
            foreach ($symlinks as $target => $link) {
                $this->createSymbolicLink($target, $link);
            }
        }
    }

    /**
     * Создание ссылки.
     *
     * @param  string  $target
     * @param  string  $link
     *
     * @return void
     */
    public function createSymbolicLink(string $target, string $link)
    {
        clearstatcache(true, $link);

        if (! $this->filesystem->exists($link)) {
            $this->filesystem->link($target, $link);
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

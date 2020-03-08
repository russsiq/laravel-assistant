<?php

namespace Russsiq\Assistant\Support\Archivist;

// Исключения.
use InvalidArgumentException;
// use Throwable;

// Базовые расширения PHP.
use SplFileInfo;
use ZipArchive;

// Зарегистрированные фасады приложения.
use Russsiq\Assistant\Facades\Archivist;

// Сторонние зависимости.
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepositoryContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Manager;
use Illuminate\Support\Traits\ForwardsCalls;
use Russsiq\Assistant\Contracts\ArchivistContract;
use Russsiq\Assistant\Contracts\Archivist\Factory as FactoryContract;

use Russsiq\Assistant\Support\Archivist\Extractor;
use Russsiq\Assistant\Support\Archivist\Packager;

class ArchivistManager implements FactoryContract
{
    use ForwardsCalls;

    /**
     * Экземпляр контейнера приложения.
     * @var Container
     */
    protected $container;

    /**
     * Экземпляр репозитория конфигураций.
     * @var ConfigRepositoryContract
     */
    protected $config;

    /**
     * Экземпляр класса по работе с файловой системой.
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Экземляр класса по работе с архивами.
     * @var ZipArchive
     */
    protected $ziparchive;

    /**
     * Массив созданных операторов (помощников).
     * @var array
     */
    protected $operators = [];

    /**
     * Коллекция файлов архивов.
     * @var array
     */
    protected $backups;

    /**
     * Создать новый экземпляр Оптимизатора.
     * @param  Container  $container
     * @param  ConsoleKernelContract  $artisan
     * @param  MessageBag  $messages
     * @param  BufferedOutput  $outputBuffer
     * @return void
     */
    public function __construct(
        Container $container
    ) {
        $this->container = $container;
        $this->config = $container->make('config');
    }

    /**
     * Получить экземпляр класса оператора.
     * @param  string|null  $name
     * @return ArchivistContract
     *
     * @throws InvalidArgumentException
     */
    public function operator(string $name = null): ArchivistContract
    {
        $name = $name ?: $this->getDefaultDriver();

        if (is_null($name)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL operator for [%s].', static::class
            ));
        }

        return $this->operators[$name]
            ?? $this->operators[$name] = $this->createOperator($name);
    }

    /**
     * Получить имя драйвера, используемого по умолчанию
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return 'backup';
    }

    /**
     * Создать новый экземпляр класса оператора.
     * @param  string  $name
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function createOperator(string $name)
    {
        $config = $this->configuration($name);

        switch ($name) {
            case 'backup':
                return $this->createBackupOperator($config);
            case 'restore':
                return $this->createRestoreOperator($config);
            default:
                throw new InvalidArgumentException(sprintf(
                    'Operator [%s] not supported.', $name
                ));
        }
    }

    protected function createBackupOperator(array $config): ArchivistContract
    {
        return new Packager(
            $this->filesystem($config),
            $this->ziparchive($config),
            $config
        );
    }

    protected function createRestoreOperator(array $config): ArchivistContract
    {
        return new Extractor(
            $this->filesystem($config),
            $this->ziparchive($config),
            $config
        );
    }

    /**
     * Получить конфигурацию поставщика услуг.
     * @param  string  $operator
     * @return array
     */
    protected function configuration(string $operator): array
    {
        // Получаем массив всех настроек Архивариуса.
        $config = $this->config->get('assistant.archivist', []);

        // Пробрасываем уровнем выше настройки выбранного поставщика.
        $config = array_merge($config, $config[$operator] ?? []);

        return $config;
    }

    /**
     * Получить экземпляр класса по работе с файловой системой.
     * @param  array  $config
     * @return Filesystem
     */
    protected function filesystem(array $config): Filesystem
    {
        return $this->container->make('files');
    }

    /**
     * Получить экземпляр класса по работе с архивами.
     * @param  array  $config
     * @return ZipArchive
     */
    protected function ziparchive(array $config): ZipArchive
    {
        return new ZipArchive();
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        $operator = null;

        if (isset($parameters[0]) && is_array($parameters[0])) {
            [Archivist::KEY_NAME_OPERATOR => $operator] = $parameters[0];
        }

        return $this->forwardCallTo($this->operator($operator), $method, $parameters);
    }
}

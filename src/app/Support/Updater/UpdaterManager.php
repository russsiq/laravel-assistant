<?php

namespace Russsiq\Assistant\Support\Updater;

// Базовые расширения PHP.
use ZipArchive;

// Сторонние зависимости.
use GuzzleHttp\Client as HttpClient;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Manager;
use Russsiq\Assistant\Support\Updater\Drivers\GithubDriver;
use Russsiq\Assistant\Support\Updater\Release;
use Russsiq\Assistant\Support\Updater\VersionFile;

/**
 * Менеджер, управляющий созданием Мастера обновлений,
 * предоставляющий доступ к его методам.
 */
class UpdaterManager extends Manager
{
    /**
     * Драйвер репозитория, используемый по умолчанию.
     * @var string
     */
    protected $defaultRepository = 'github';

    /**
     * Получить имя драйвера, используемого по умолчанию
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('assistant.updater.driver', $this->defaultRepository);
    }

    /**
     * Задать имя драйвера репозитория, используемого по умолчанию.
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver(string $name)
    {
        $this->config->set('assistant.updater.driver', $name);
    }

    /**
     * Создать экземпляр Мастера обновлений
     * с использованием драйвера Github.
     * @return GithubDriver
     */
    protected function createGithubDriver(): GithubDriver
    {
        $config = $this->config->get('assistant.updater', []);
        $config = array_merge($config, $config['github'] ?? []);

        return new GithubDriver(
            $this->release($config),
            $config
        );
    }

    /**
     * Получить экземпляр Релиза.
     * @param  array  $config
     * @return Release
     */
    protected function release(array $config): Release
    {
        return new Release(
            $this->httpClient($config),
            $this->fileSystem($config),
            $this->versionFile($config),
            $this->zipArchive($config),
            $config
        );
    }

    /**
     * Получить экземпляр HTTP клиента.
     * @param  array  $config
     * @return HttpClient
     */
    protected function httpClient(array $config): HttpClient
    {
        $params = $config['guzzle'] ?? [];

        return new HttpClient($params);
    }

    /**
     * Получить экземпляр класса по работе с файловой системой.
     * @param  array  $config
     * @return Filesystem
     */
    protected function fileSystem(array $config): Filesystem
    {
        return $this->container->make('files');
    }

    /**
     * Получить экземпляр класса файла версионирования.
     * @param  array  $config
     * @return VersionFile
     */
    protected function versionFile(array $config): VersionFile
    {
        $params = $config['version_file'] ?? [];

        return new VersionFile($this->container->make('cache'), $params);
    }

    /**
     * Получить экземпляр класса по работе с архивами.
     * @param  array  $config
     * @return ZipArchive
     */
    protected function zipArchive(array $config): ZipArchive
    {
        return new ZipArchive();
    }
}

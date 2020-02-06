<?php

namespace Russsiq\Assistant\Support\Updater;

use Illuminate\Support\Manager;

use Russsiq\Assistant\Support\Updater\Drivers\GithubDriver;
use Russsiq\Assistant\Support\Updater\Release;

class UpdaterManager extends Manager
{
    /**
     * Драйвер репозитория, используемый по умолчанию.
     *
     * @var string
     */
    protected $defaultRepository = 'github';

    /**
     * Получить имя драйвера, используемого по умолчанию
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('assistant.updater.driver', $this->defaultRepository);
    }

    /**
     * Задать имя драйвера репозитория, используемого по умолчанию
     *
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
     *
     * @return GithubDriver
     */
    protected function createGithubDriver(): GithubDriver
    {
        $config = $this->config->get('assistant.updater', []);
        $config = array_merge($config, $config['github'] ?? []);

        return new GithubDriver(new Release($config), $config);
    }
}

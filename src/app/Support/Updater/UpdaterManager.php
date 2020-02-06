<?php

namespace Russsiq\Assistant\Support\Updater;

use Illuminate\Support\Manager;

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
}

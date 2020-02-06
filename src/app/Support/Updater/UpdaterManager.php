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
}

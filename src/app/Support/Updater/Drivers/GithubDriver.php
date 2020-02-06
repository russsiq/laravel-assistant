<?php

namespace Russsiq\Assistant\Support\Updater\Drivers;

use Russsiq\Assistant\Support\Updater\AbstractUpdater;

class GithubDriver extends AbstractUpdater
{
    /**
     * Массив параметров экземпляра класса.
     *
     * @var array
     */
    private $params = [
        'exclude_directories' => [],
        'allowed_files' => [],
        'destination_path' => null,

    ];

    /**
     * Создать новый экземпляр Мастера обновлений
     * с использованием драйвера Github.
     *
     * @param array  $params
     * @return void
     */
    public function __construct(array $params = [])
    {
        $this->configure($params);
    }

    /**
     * Конфигурирование параметров экземпляра класса.
     *
     * @param  array  $params
     * @return $this
     */
    public function configure(array $params = [])
    {
        if (isset($params['exclude_directories']) and is_array($params['exclude_directories'])) {
            // Задать папки, игнорируемые во время процесса обновления.
            $this->params['exclude_directories'] = $params['exclude_directories'];
        }

        if (isset($params['allowed_files']) and is_array($params['allowed_files'])) {
            // Задать файлы, расположеные в корне приложения, которые будут обновлены.
            $this->params['allowed_files'] = $params['allowed_files'];
        }

        // Задать корневую директорию обновляемого приложения.
        $this->params['destination_path'] = base_path('new');

        return $this;
    }
}

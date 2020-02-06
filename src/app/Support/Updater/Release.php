<?php

namespace Russsiq\Assistant\Support\Updater;

/**
 * Класс, отвечающий за получения как сведений о релизе,
 * так и за загрузку самого релизе из репозитория.
 */
class Release
{
    /**
     * Массив параметров экземпляра класса.
     *
     * @var array
     */
    private $params = [
        'download_path' => null,
        'endpoint' => null,
        'source_key' => null,
        'version_key' => null,
        'version_format' => null,

    ];

    /**
     * Создать новый экземпляр Релиза.
     *
     * @param array  $params
     * @return void
     */
    public function __construct(
        array $params = []
    ) {
        $this->configure($params);
    }

    /**
     * Конфигурирование параметров экземпляра класса.
     *
     * @param  array $params
     *
     * @return $this
     */
    public function configure(array $params = [])
    {
        if (isset($params['download_path'])) {
            // Задать директорию загрузки исходников обновляемого приложения.
            $this->params['download_path'] = $params['download_path'];
        }

        if (isset($params['endpoint'])) {
            // Задать URL-адрес для сбора сведений о последнем релизе.
            $this->params['endpoint'] = $params['endpoint'];
        }

        if (isset($params['source_key'])) {
            // Задать ключ, обозначающий имя ссылки на загрузки исходников релиза.
            $this->params['source_key'] = $params['source_key'];
        }

        if (isset($params['version_key'])) {
            // Задать ключ, обозначающий имя номера версии релиза.
            $this->params['version_key'] = $params['version_key'];
        }

        if (isset($params['version_format'])) {
            // Задать формат имени исходника, загружаемого из репозитория.
            $this->params['version_format'] = $params['version_format'];
        }

        return $this;
    }
}

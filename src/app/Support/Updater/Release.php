<?php

namespace Russsiq\Assistant\Support\Updater;

use Exception;
use InvalidArgumentException;
use RuntimeException;

use ZipArchive;

use GuzzleHttp\ClientInterface;

use Illuminate\Filesystem\Filesystem;

/**
 * Класс, отвечающий за получения как сведений о релизе,
 * так и за загрузку самого релизе из репозитория.
 */
class Release
{
    /**
     * Экземпляр HTTP клиента.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * Экземпляр класса по работе с файловой системой.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Экземпляр класса, отвечающего за
     * хранение информации о доступном релизе.
     *
     * @var VersionFile
     */
    protected $versionfile;

    /**
     * Экземляр класса по работе с архивами.
     *
     * @var ZipArchive
     */
    protected $ziparchive;

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
     * Массив оригинальных полей, полученных при загрузке
     * сведений о последнем релизе из репозитория.
     *
     * @var array
     */
    public $fields = [];

    /**
     * Создать новый экземпляр Релиза.
     *
     * @param ClientInterface  $client
     * @param Filesystem  $filesystem
     * @param VersionFile  $versionfile
     * @param ZipArchive  $ziparchive
     * @param array  $params
     * @return void
     */
    public function __construct(
        ClientInterface $client,
        Filesystem $filesystem,
        VersionFile $versionfile,
        ZipArchive $ziparchive,
        array $params = []
    ) {
        $this->client = $client;
        $this->filesystem = $filesystem;
        $this->versionfile = $versionfile;
        $this->ziparchive = $ziparchive;

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

    /**
     * Получить временную директорию, куда будут загружены
     * исходники обновляемого приложения. По умолчанию: `storage/tmp`.
     *
     * @param  string  $path
     * @return string
     *
     * @throws Exception Не указана директория загрузки исходников обновляемого приложения.
     */
    public function downloadPath(string $path = ''): string
    {
        return $this->params['download_path'].($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Получить URL-адрес для сбора сведений о последнем релизе.
     *
     * @return string
     *
     * @throws Exception Не указана ссылка для сбора сведений о последнем релизе.
     */
    public function endpoint(): string
    {
        return $this->params['endpoint'];
    }

    /**
     * Получить ключ, обозначающий имя ссылки на загрузки исходников релиза.
     *
     * @return string
     *
     * @throws Exception Не указан ключ имени ссылки на загрузки исходников релиза.
     */
    public function sourceKey(): string
    {
        return $this->params['source_key'];
    }

    /**
     * Получить ключ, обозначающий имя номера версии релиза.
     *
     * @return string
     *
     * @throws Exception Не указан ключ имени номера версии релиза.
     */
    public function versionKey(): string
    {
        return $this->params['version_key'];
    }

    /**
     * Получить формат имени исходника, загружаемого из репозитория.
     *
     * @return string
     *
     * @throws Exception Не указан формат имени исходника, загружаемого из репозитория.
     */
    public function versionFormat(): string
    {
        return $this->params['version_format'];
    }
}

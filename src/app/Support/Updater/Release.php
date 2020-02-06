<?php

namespace Russsiq\Assistant\Support\Updater;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

use ZipArchive;

use GuzzleHttp\ClientInterface;

use Illuminate\Filesystem\Filesystem;

use Psr\Http\Message\ResponseInterface;

/**
 * Класс, отвечающий за получения как сведений о релизе,
 * так и за загрузку самого релизе из репозитория.
 */
class Release
{
    /**
     * Код успешного ответа.
     *
     * @var int
     */
    const HTTP_OK = 200;

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
     * Массив зарегистрированных замыканий,
     * вызываемых по окончании загрузки сведений о релизе.
     *
     * @var array
     */
    private $afterLoad = [];

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

    /**
     * Задать ссылку на репозиторий для загрузки релиза.
     *
     * @param  string  $url
     *
     * @return $this
     */
    public function setSourceUrl(string $url)
    {
        $this->versionfile->set('source_url', $url);

        return $this;
    }

    /**
     * Получить ссылку на репозиторий для загрузку релиза.
     *
     * @return string
     *
     * @throws Exception Не указана ссылка на репозиторий для загрузку релиза.
     */
    public function sourceUrl(): string
    {
        if ($this->versionfile->doesntExist()) {
            $this->loadInfo();
        }

        return $this->versionfile->get('source_url');
    }

    /**
     * Задать номер доступной версии релиза, опубликованного в репозитории.
     *
     * @param  string  $version
     *
     * @return $this
     */
    public function setVersion(string $version)
    {
        $this->versionfile->set('version', $version);

        return $this;
    }

    /**
     * Получить номер доступной версии релиза, опубликованного в репозитории.
     *
     * @return string
     *
     * @throws Exception Не указана версия релиза.
     */
    public function version(): string
    {
        if ($this->versionfile->doesntExist()) {
            $this->loadInfo();
        }

        return $this->versionfile->get('version');
    }

    /**
     * Загрузить информацию о последнем релизе из репозитория.
     *
     * @return $this
     */
    public function loadInfo()
    {
        $response = $this->client->request('GET', $this->endpoint());
        $this->assertResponseIsSuccessful($response);

        $release = json_decode($response->getBody());
        $this->assertJsonIsValid(json_last_error());

        // Сохраняем оригинальные поля из ответа.
        $this->fields = (array) $release;

        // Устанавливаем номер доступной версии и
        // ссылку на загрузку релиза из репозитория.
        // Обязательно сохраняем в кэш информацию.
        $this->setVersion($release->{$this->versionKey()})
            ->setSourceUrl($release->{$this->sourceKey()})
            ->saveInfo();

        // Применяем все замыкания, добавленные в драйвере,
        // в которых можно поменять значение необходимых полей
        // и пересохранить информацию о релизе в кэше.
        foreach ($this->afterLoad as $afterLoad) {
            $afterLoad();
        }

        return $this;
    }

    /**
     * Сохранить информацию о последнем релизе.
     *
     * @return bool
     */
    public function saveInfo(): bool
    {
        return $this->versionfile->save();
    }

    /**
     * Добавить замыкание, которое будет выполняться
     * каждый раз после загрузки сведений о релизе.
     *
     * @param  callable  $callback
     *
     * @return $this
     */
    public function afterLoad(callable $callback)
    {
        $this->afterLoad[] = function () use ($callback) {
            return call_user_func_array($callback, [$this]);
        };

        return $this;
    }

    /**
     * Определить, что ответ имеет статус успешного.
     *
     * @param  ResponseInterface  $response
     *
     * @return void
     *
     * @throws Exception
     */
    protected function assertResponseIsSuccessful(ResponseInterface $response)
    {
        if ($response->getStatusCode() !== self::HTTP_OK) {
            throw new Exception(
                'Не удалось получить сведения о релизе. Проверьте доступность репозитория.'
            );
        }
    }

    /**
     * Определить, произошла ли ошибка во время декодирования JSON.
     *
     * @param  int  $jsonError
     *
     * @return void
     *
     * @throws Exception
     */
    protected function assertJsonIsValid(int $jsonError)
    {
        if ($jsonError !== JSON_ERROR_NONE) {
            throw new Exception(json_last_error_msg());
        }
    }
}

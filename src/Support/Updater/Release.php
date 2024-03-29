<?php

namespace Russsiq\Assistant\Support\Updater;

use Exception;
use GuzzleHttp\ClientInterface;
use Illuminate\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use SplFileInfo;
use Throwable;
use ZipArchive;

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
     * Расширение файла с архивом.
     *
     * @var string
     */
    const ZIP_EXTENSION = 'zip';

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
     * Экземпляр класса по работе с архивами.
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
     * @param  array  $params
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
     * Получить полный путь к сохраняемому/загруженному исходнику релиза.
     *
     * @param  string  $version
     * @return string
     */
    public function storageFile(string $version): string
    {
        return $this->downloadPath($version.'.'.self::ZIP_EXTENSION);
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
     * Получить имя исходника, загружаемого из репозитория.
     *
     * @return string
     */
    public function sourceFilename(): string
    {
        return preg_replace('/__VERSION__/', $this->version(), $this->versionFormat());
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
     * Задать характеристику кретичности релиза.
     *
     * @param  bool  $isCritical
     * @return $this
     */
    public function setIsCritical(bool $isCritical)
    {
        $this->versionfile->set('is_critical', $isCritical);

        return $this;
    }

    /**
     * Получить характеристику кретичности релиза.
     *
     * @return bool
     *
     * @throws Exception Не указана характеристика кретичности релиза.
     */
    public function isCritical(): bool
    {
        if ($this->versionfile->doesntExist()) {
            $this->loadInfo();
        }

        return $this->versionfile->get('is_critical');
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
     * Загрузить архив новой версии приложения
     * из репозитория с помощью НТТР-метода GЕТ.
     *
     * @return void
     */
    public function fetch()
    {
        // Если нет ссылки на загрузку исходника,
        // то обновится информация о релизе.
        $sourceUrl = $this->sourceUrl();
        $version = $this->version();

        $storagePath = $this->downloadPath($version);
        // Полный путь к сохраняемому/загруженному исходнику.
        $storageFile = $this->storageFile($version);

        if (! $this->alreadyFetched($storageFile)) {
            $this->download($sourceUrl, $storageFile);
        }
    }

    /**
     * Проверить, что исходники уже были подгружены.
     *
     * @param  string  $file
     * @return bool
     */
    protected function alreadyFetched(string $file): bool
    {
        return $this->filesystem->isFile($file)
            && $this->filesystem->mimeType($file) === 'application/zip';
    }

    /**
     * Загрузить файл из указанного источника в указанное место.
     *
     * @param  string  $sourceUrl
     * @param  string  $storageFile
     * @return ResponseInterface
     */
    protected function download(string $sourceUrl, string $storageFile): ResponseInterface
    {
        @ini_set('max_execution_time', 120);

        $this->ensureDirectoryExists($this->filesystem->dirname($storageFile));

        return $this->client->request('GET', $sourceUrl, [
            'sink' => $storageFile,

        ]);
    }

    /**
     * Создать директорию, если она отсутствует.
     *
     * @param  string  $path
     * @return void
     */
    protected function ensureDirectoryExists(string $path)
    {
        if ($this->filesystem->missing($path)) {
            $this->filesystem->makeDirectory($path, 0777, true, true);
        }
    }

    /**
     * Извлечь архив с исходниками для последующего обновления.
     *
     * @param  string  $filename
     * @param  string  $destination
     * @return bool
     */
    public function unzipArchive(string $filename, string $destination): bool
    {
        @ini_set('max_execution_time', 120);

        try {
            $opened = $this->ziparchive->open($filename);
            $this->assertZiparchiveIsOpened($filename, $opened);

            $extracted = $this->ziparchive->extractTo($destination);
            $this->assertZiparchiveIsExtracted($filename, $extracted);

            $this->ziparchive->close();

            $this->ensureSourceInRootDirectory($destination);

            return true;
        } catch (Throwable $e) {
            $this->filesystem->delete($filename);

            throw $e;
        }
    }

    /**
     * Убедиться, что извлеченные файлы не имеют
     * посторонней вложенной директории,
     * т.е. исходники расположены в корневой директории.
     *
     * @param  string  $destination
     * @return void
     */
    protected function ensureSourceInRootDirectory(string $destination)
    {
        $directories = $this->filesystem->directories($destination);

        if (1 === count($directories)) {
            $root = $directories[0];

            collect($this->filesystem->directories($root))
                ->each(function (string $directory) use ($destination) {
                    $this->filesystem->moveDirectory(
                        $directory,
                        $destination.DIRECTORY_SEPARATOR.$this->filesystem->name($directory)
                    );
                });

            collect($this->filesystem->files($root, true))
                ->each(function (SplFileInfo $file) use ($destination) {
                    $this->filesystem->move(
                        $file->getRealPath(),
                        $destination.DIRECTORY_SEPARATOR.$file->getFilename()
                    );
                });

            $this->filesystem->deleteDirectory($root);
        }
    }

    /**
     * Определить, произошла ли ошибка во время открытия архива.
     *
     * @param  string  $filename
     * @param  mixed  $opened
     * @return void
     *
     * @throws RuntimeException
     */
    protected function assertZiparchiveIsOpened(string $filename, $opened)
    {
        if ($opened !== true) {
            throw new RuntimeException(sprintf(
                "Cannot open zip archive [%s].",
                $filename
            ));
        }
    }

    /**
     * Определить, произошла ли ошибка во время извлечения файлов из архива.
     *
     * @param  string  $filename
     * @param  mixed  $extracted
     * @return void
     *
     * @throws RuntimeException
     */
    protected function assertZiparchiveIsExtracted(string $filename, $extracted)
    {
        if ($extracted !== true) {
            throw new RuntimeException(sprintf(
                "Unable to extract zip archive [%s].",
                $filename
            ));
        }
    }

    /**
     * Определить, что ответ имеет статус успешного.
     *
     * @param  ResponseInterface  $response
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

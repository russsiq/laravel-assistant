<?php

namespace Russsiq\Assistant\Support\Updater\Drivers;


use Russsiq\Assistant\Support\Updater\AbstractUpdater;
use Russsiq\Assistant\Support\Updater\Release;

/**
 * Экземпляр Мастера обновлений с использованием драйвера Github.
 */
class GithubDriver extends AbstractUpdater
{
    /**
     * Экземпляр релиза.
     * @var Release
     */
    protected $release;

    /**
     * Массив параметров экземпляра класса.
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
     * @param Release  $release
     * @param array  $params
     * @return void
     */
    public function __construct(
        Release $release,
        array $params = []
    ) {
        $this->release = $release;

        $this->configure($params);
        $this->addHooksToRelease();
    }

    /**
     * Конфигурирование параметров экземпляра класса.
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
        $this->params['destination_path'] = base_path();

        return $this;
    }

    /**
     * Добавить хуки к экземпляру релиза.
     * Данные замыкания будут выполняться каждый раз
     * при получении сведений из репозитория о релизе.
     * @return void
     */
    protected function addHooksToRelease()
    {
        $this->release->afterLoad(function (Release $release) {
            // Ищем вложение с именем, совпадающем с именем исходника.
            $asset = collect($release->fields['assets'])
                ->firstWhere('name', $release->sourceFilename());

            // Если найдено вложение с именем файла релиза,
            // то считаем, что это критическое обновление.
            // Меняем ссылку на загрузку исходника и сохраняем информацию.
            if ($asset) {
                $release->setSourceUrl($asset->browser_download_url)
                    ->setIsCritical(true)
                    ->saveInfo();
            }
        });
    }

    /**
     * Получить массив папок, игнорируемых во время процесса обновления.
     * @return array
     */
    protected function excludeDirectories(): array
    {
        // Если это критическое обновление,
        // то необходимо удалить директорию `vendor`
        // из списка исключаемых директорий.
        if ($this->release->isCritical()) {
            $finded = array_search('vendor', $this->params['exclude_directories']);

            if($finded !== false) {
                unset($this->params['exclude_directories'][$finded]);
            }
        }

        return $this->params['exclude_directories'];
    }

    /**
     * Получить массив файлов, которые расположены
     * в корне приложения и будут обновлены.
     * @return array
     */
    protected function allowedFiles(): array
    {
        return $this->params['allowed_files'];
    }

    /**
     * Корневая директория обновляемого приложения.
     * @return string
     */
    protected function destinationPath(): string
    {
        return $this->params['destination_path'];
    }

    /**
     * Получить временную директорию, где расположены
     * исходники файлов обновляемого приложения.
     * @return string
     */
    protected function sourcePath(): string
    {
        return $this->release->downloadPath($this->availableVersion());
    }

    /**
     * Получить полный путь к загруженному исходнику релиза.
     * @return string
     */
    protected function storageFile(): string
    {
        return $this->release->storageFile($this->availableVersion());
    }

    /**
     * Получить номер доступной версии приложения,
     * опубликованного в репозитории.
     * @return string
     */
    public function availableVersion(): string
    {
        return $this->release->version();
    }

    /**
     * Загрузить архив новой версии приложения
     * из репозитория с помощью НТТР-метода GЕТ.
     * @return void
     */
    public function fetch()
    {
        return $this->release->fetch();
    }

    /**
     * Запустить процесс обновления приложения до актуальной версии.
     * @return bool
     */
    public function update(): bool
    {
        // Локальные переменные.
        $sourcePath = $this->sourcePath();
        $storageFile = $this->storageFile();
        $destinationPath = $this->destinationPath();

        // Предварительно выполняем проверку файлов на перезапись.
        $this->assertFilesInDirectoryIsWritable($destinationPath);

        // // @NOTE Возможно, что данный метод можно было бы просто делегировать.
        // $this->release->applyAvailableVersion($storageFile, $sourcePath, $destinationPath);

        // Распаковываем исходники релиза из архива.
        $this->release->unzipArchive($storageFile, $sourcePath);

        // Копируем содержимое директории исходников в конечную директорию приложения.
        $this->copySourceDirectory($sourcePath, $destinationPath);

        // Обновить информацию о версии приложения.
        $this->updateCurrentlyVersion();

        return true;
    }
}

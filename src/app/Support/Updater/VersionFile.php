<?php

namespace Russsiq\Assistant\Support\Updater;

// Исключения.
use Exception;
use InvalidArgumentException;
use RuntimeException;

// Сторонние зависимости.
use Illuminate\Contracts\Cache\Factory as CacheFactoryContract;
use Illuminate\Contracts\Cache\Repository;

/**
 * Класс, отвечающий за хранение информации о доступном релизе,
 * непосредственно в файле версионирования.
 */
class VersionFile
{
    /**
     * Менеджер кеша.
     * @NB: Помечен здесь как репозиторий для подсказок в редакторе.
     *
     * @var Repository
     */
    protected $cache;

    /**
     * Настройки файла версионирования по умолчанию.
     *
     * @var array
     */
    protected $params = [
        // Имя файла со сведениями о новой версии.
        'filename' => 'assistant-new-version',

        // Время хранения файла версионирования в секундах.
        'store_time' => 86400,

    ];

    /**
     * Динамическое содержимое файла версионирования.
     *
     * @var array
     */
    protected $content = [
        'version' => null,
        'source_url' => null,

    ];

    /**
     * Разрешенные поля, которые должны содержаться в файле версионирования.
     *
     * @var array
     */
    protected $allowed = [
        'version',
        'source_url',

    ];

    /**
     * Содержимое файла уже было подгружено.
     *
     * @var bool
     */
    protected $isLoaded = false;

    /**
     * Создать новый экземпляр класса версионирования.
     *
     * @param  CacheFactoryContract  $cache
     * @param  array  $params
     * @return void
     */
    public function __construct(
        CacheFactoryContract $cache,
        array $params = []
    ) {
        $this->cache = $cache;

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
        if (isset($params['filename']) and is_string($params['filename'])) {
            $this->params['filename'] = $params['filename'];
        }

        if (isset($params['store_time']) and is_int($params['store_time'])) {
            $this->params['store_time'] = $params['store_time'];
        }

        return $this;
    }

    /**
     * Получить Имя файла версионирования.
     *
     * @return string
     */
    public function filename(): string
    {
        return $this->params['filename'];
    }

    /**
     * Получить Время хранения файла версионирования.
     *
     * @return int
     */
    public function storeTime(): int
    {
        return $this->params['store_time'];
    }

    /**
     * Проверить существование файла версионирования.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->cache->has($this->filename());
    }

    /**
     * Проверить отсутствие файла версионирования.
     *
     * @return bool
     */
    public function doesntExist(): bool
    {
        return ! $this->exists();
    }

    /**
     * Установить значение для указанного поля.
     *
     * @param  string  $key
     * @param  string  $value
     * @return $this
     */
    public function set(string $key, string $value)
    {
        $this->assertKeyInAllowed($key);

        $this->content[$key] = $value;

        return $this;
    }

    /**
     * Получить информацию о поле.
     *
     * @param  string  $key
     * @return string  Возвращаемое значение всегда должно быть строкой.
     *
     * @throws Exception  Значение для данного поля не задано.
     */
    public function get(string $key): string
    {
        $this->assertKeyInAllowed($key);

        $this->attemptLoad();

        return $this->content[$key];
    }

    /**
     * Попытаться загрузить файл со сведениями,
     * если он еще не был загружен.
     *
     * @return bool
     */
    protected function attemptLoad(): bool
    {
        return $this->isLoaded or $this->load();
    }

    /**
     * Загрузить содержимое файла со сведениями.
     *
     * @return bool
     */
    protected function load(): bool
    {
        $this->setLoaded(true);

        $content = $this->cache->get($this->filename());

        $this->assertContentIsValid($content);

        foreach ($content as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }

    /**
     * Установить значение маркера, информирующее о том,
     * было ли подгружено содержимое файла.
     *
     * @param  bool  $loaded
     * @return $this
     */
    protected function setLoaded(bool $loaded)
    {
        $this->isLoaded = $loaded;

        return $this;
    }

    /**
     * Сохранить информацию о полях в файл версионирования.
     *
     * @return bool
     */
    public function save(): bool
    {
        $this->assertContentIsValid($this->content);

        return $this->cache->put($this->filename(), $this->content, $this->storeTime());
    }

    /**
     * Удалить файл версионирования.
     *
     * @return bool
     */
    public function forget(): bool
    {
        return $this->cache->forget($this->filename());
    }

    /**
     * Определить, что поле находится в списке разрешенных.
     *
     * @param  string  $key
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    protected function assertKeyInAllowed(string $key): bool
    {
        if (in_array($key, $this->allowed)) {
            return true;
        }

        throw new InvalidArgumentException(sprintf(
            "Переданный ключ [%s] не указан в списке разрешенных.",
            $key
        ));
    }

    /**
     * Определить, что содержимое валидно.
     *
     * @param  array  $content
     * @return bool
     *
     * @throws RuntimeException
     */
    protected function assertContentIsValid(array $content): bool
    {
        // Проверка сводится к сравнению массивов
        // отфильтрованных не `NULL` элементов
        // и разрешенных к сохранению полей.
        $keys = array_keys(array_filter($content, function ($value, $key) {
            return ! is_null($value);
        }, ARRAY_FILTER_USE_BOTH));

        if (empty(array_diff($this->allowed, $keys))) {
            return true;
        }

        // Содержимое не валидно, удаляем файл без раздумий.
        $this->forget();

        throw new RuntimeException('Содержимое файла версионирования не валидно.');
    }
}

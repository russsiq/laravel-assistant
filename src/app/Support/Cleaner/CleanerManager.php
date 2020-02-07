<?php

namespace Russsiq\Assistant\Support\Cleaner;

use Artisan;
use SplFileInfo;

// use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use Russsiq\Assistant\Contracts\CleanerContract;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

class CleanerManager implements CleanerContract
{
    /**
     * Ключ кэша сообщений.
     *
     * @var string
     */
    const MESSAGES_KEY_CACHE = 'laravel-cleaner:messages';

    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Экземпляр Ядра консоли.
     *
     * @var ConsoleKernelContract
     */
    protected $artisan;

    /**
     * Экземпляр Коллекционера сообщений.
     *
     * @var MessageBag
     */
    protected $messages;

    /**
     * Экземпляр Буфера вывода сообщений консоли.
     *
     * @var BufferedOutput
     */
    protected $outputBuffer;

    /**
     * Создать новый экземпляр Оптимизатора.
     *
     * @param Application  $app
     * @param ConsoleKernelContract  $artisan
     * @param MessageBag  $messages
     * @param BufferedOutput  $outputBuffer
     */
    public function __construct(Application $app, ConsoleKernelContract $artisan, MessageBag $messages, BufferedOutput $outputBuffer)
    {
        $this->app = $app;
        $this->artisan = $artisan;
        $this->messages = $messages;
        $this->outputBuffer = $outputBuffer;
    }

    /**
     * Получить Ключ кэша сообщений.
     *
     * @return string
     */
    public static function getMessagesCacheKey(): string
    {
        return self::MESSAGES_KEY_CACHE;
    }

    /**
     * Получить все сообщения по каждому ключу.
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages->all();
    }

    /**
     * Добавить сообщение в Коллекционер.
     *
     * @param  string  $key
     * @param  string  $message
     *
     * @return void
     */
    public function addMessage(string $key, string $message)
    {
        cache()->put(
            self::getMessagesCacheKey(),
            $this->messages->add($key, $message)->all()
        );
    }

    /**
     * Очистка кэша приложения.
     *
     * @return void
     */
    public function clearCache()
    {
        $this->artisanCall('cache:clear');
    }

    /**
     * Очистка кэша по ключу.
     *
     * @return void
     */
    public function clearCacheByKey(string $key, string $delimiter = '|')
    {
        foreach (explode($delimiter, $key) as $k) {
            cache()->forget($k);
        }

        if (! request()->ajax()) {
            return redirect()->back()->withStatus(
                trans('assistant::clean.messages.success.cache_cleared')
            );
        }
    }

    /**
     * Очистка кэша конфигураций приложения.
     *
     * @return void
     */
    public function clearCompiled()
    {
        $this->artisanCall('clear-compiled');
    }

    /**
     * Очистка кэша конфигураций приложения.
     *
     * @return void
     */
    public function cacheConfig()
    {
        $this->artisanCall('config:cache');
    }

    /**
     * Очистка кэша конфигураций приложения.
     *
     * @return void
     */
    public function clearConfig()
    {
        $this->artisanCall('config:clear');
    }

    /**
     * Очистка кэша маршрутов приложения.
     *
     * @return void
     */
    public function cacheRoute()
    {
        $this->artisanCall('route:cache');
    }

    /**
     * Очистка кэша маршрутов приложения.
     *
     * @return void
     */
    public function clearRoute()
    {
        $this->artisanCall('route:clear');
    }

    /**
     * Очистка скомпилированных шаблонов приложения.
     *
     * @return string
     */
    public function clearView()
    {
        return $this->artisanCall('view:clear');
    }

    public function clearStatCache()
    {
        if (function_exists('clearstatcache')) {
            clearstatcache();

            $this->addMessage('clear_stat_cache', 'File status cache cleared!');
        }
    }

    public function clearXCache()
    {
        if (function_exists('xcache_clear_cache')) {
            xcache_clear_cache(XC_TYPE_PHP);

            $this->addMessage('clear_x_cache', 'XCache cleared!');
        }
    }

    public function clearOpCache()
    {
        if (function_exists('opcache_invalidate')) {
            collect(
                $this->app->make('files')
                    ->allFiles([
                        $this->app->basePath('app'),
                        $this->app->basePath('bootstrap'),
                        $this->app->basePath('resources'),
                        $this->app->basePath('storage/framework/views'),
                    ])
            )->filter(function (SplFileInfo $file) {
                return 'php' === $file->getExtension();
            })->each(function (SplFileInfo $file) {
                opcache_invalidate($file->getRealPath(), true);
            });

            $this->addMessage('clear_op_cache', 'OpCache cleared!');
        }
    }

    /**
     * Комплексная очистка и последующее кэширование.
     *
     * @return void
     */
    public function complexOptimize()
    {
        $this->process([
            'clear_cache',
            'clear_view',
            'clear_compiled',
            // 'debugbar:clear',

            'clear_stat_cache',
            'clear_x_cache',
            'clear_op_cache',

            'cache_config',
            'cache_route',

        ]);
    }

    /**
     * Запустить команду консоли Artisan.
     *
     * @param  string $name
     * @param  array  $options
     *
     * @return int
     */
    protected function artisanCall(string $name, array $options = []): int
    {
        $exitCode = $this->artisan->call($name, $options, $this->outputBuffer);

        array_map(
            function ($message) use ($name) {
                $this->addMessage($name, trim($message));
            },
            preg_split('/\r\n|\n|\r/', $this->outputBuffer->fetch(), null, PREG_SPLIT_NO_EMPTY)
        );

        return $exitCode;
    }

    /**
     * Запустить внутренние методы очистки, кэширования, оптимизации.
     *
     * @param  array  $methods Массив методов.
     *
     * @return void
     */
    public function process(array $methods)
    {
        foreach ($methods as $method) {
            $this->{$method}();
        }
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __call(string $method, array $parameters)
    {
        $dinamic = Str::camel($method);

        if (method_exists($this, $dinamic)) {
            return $this->{$dinamic}(...$parameters);
        }

        throw new \InvalidArgumentException(
            "Method [{$method}] missing from ".get_class($this)
        );
    }
}

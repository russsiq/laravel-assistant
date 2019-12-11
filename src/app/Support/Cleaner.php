<?php

namespace Russsiq\Assistant\Support;

use Artisan;

// use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use Russsiq\Assistant\Support\Contracts\CleanerContract;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

class Cleaner implements CleanerContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Экземпляр приложения.
     *
     * @var ConsoleKernelContract
     */
    protected $artisan;

    /**
     * Экземпляр приложения.
     *
     * @var MessageBag
     */
    protected $messages;

    /**
     * Экземпляр приложения.
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

    public function getMessages()
    {
        return cache()->pull('laravel-cleaner:messages');
    }

    /**
     * Очистка кэша приложения.
     *
     * @return string
     */
    public function clearCache()
    {
        return $this->artisanCall('cache:clear');
    }

    /**
     * Очистка кэша по ключу.
     *
     * @return string
     */
    public function clearCacheByKey(string $key)
    {
        foreach (explode('|', $key) as $k) {
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
    public function cacheConfig()
    {
        return $this->artisanCall('config:cache');
    }

    /**
     * Очистка кэша конфигураций приложения.
     *
     * @return void
     */
    public function clearConfig()
    {
        return $this->artisanCall('config:clear');
    }

    /**
     * Очистка кэша маршрутов приложения.
     *
     * @return void
     */
    public function cacheRoute()
    {
        return $this->artisanCall('route:cache');
    }

    /**
     * Очистка кэша маршрутов приложения.
     *
     * @return void
     */
    public function clearRoute()
    {
        return $this->artisanCall('route:clear');
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
        clearstatcache();

        $this->messages->add('clear_stat_cache', 'File status cache cleared!');
        cache()->put('laravel-cleaner:messages', $this->messages->all());
    }

    public function clearXCache()
    {
        if (function_exists('xcache_get')) {
            xcache_clear_cache(XC_TYPE_PHP);

            $this->messages->add('clear_x_cache', 'XCache cleared!');
            cache()->put('laravel-cleaner:messages', $this->messages->all());
        }
    }

    public function clearOpCache()
    {
        if (function_exists('opcache_invalidate')) {
            $filesystem = $this->app['files'];

            collect($filesystem->allFiles([
                base_path('app'),
                base_path('bootstrap'),
                base_path('resources'),
                base_path('storage/framework/views'),
            ]))->filter(function ($file) {
                return 'php' === $file->getExtension();
            })->each(function ($file) {
                opcache_invalidate($file->getRealPath(), true);
            });

            $this->messages->add('clear_op_cache', 'OpCache cleared!');
            cache()->put('laravel-cleaner:messages', $this->messages->all());
        }
    }

    /**
     * Комплексная очистка.
     *
     * @return void
     */
    public function complexClear(): array
    {
        return $this->proccess([
            'clear_stat_cache',

            'clear_cache',
            'clear_config',
            'clear_route',
            'clear_view',
            // 'debugbar:clear',

            'clear_x_cache',
            'clear_op_cache',

        ]);
    }

    public function complexCache()
    {
        return $this->proccess([

        ]);
    }

    /**
     * Комплексная очистка и последующее кэширование.
     *
     * @return void
     */
    public function complexOptimize()
    {
        $this->complexClear();
    }

    /**
     * Запустить команду консоли Artisan.
     *
     * @param  string $name [description]
     *
     * @return string
     */
    protected function artisanCall(string $name, array $options = [])
    {
        if (0 === $this->artisan->call($name, $options, $this->outputBuffer)) {
            array_map(
                function ($message) use ($name) {
                    $this->messages->add($name, trim($message));
                },
                preg_split('/\r\n|\n|\r/', $this->outputBuffer->fetch(), null, PREG_SPLIT_NO_EMPTY)
            );

            cache()->put('laravel-cleaner:messages', $this->messages->all());
        }
    }

    // /**
    //  * Запустить команду консоли Artisan.
    //  *
    //  * @param  string $name [description]
    //  *
    //  * @return string
    //  */
    // protected function artisanCallSilent(string $name, array $options = [])
    // {
    //     $this->artisan->call($name, $options, new NullOutput);
    //
    //     $array = array_map(function ($message) use ($name) {
    //             $this->messages->add($name, trim($message));
    //         },
    //
    //         preg_split('/\r\n|\n|\r/', $this->outputBuffer->fetch(), null, PREG_SPLIT_NO_EMPTY)
    //     );
    //
    //     dump([
    //         $name => $this->messages->first($name)
    //     ]);
    // }

    public function proccess(array $methods): array
    {
        $messages = [];

        foreach ($methods as $method) {
            if ($result = $this->{$method}()) {
                foreach ((array) $result as $message) {
                    $messages[] = $message;
                }
            }
        }

        return $messages;
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
    public function __call($method, $parameters)
    {
        $dinamic = Str::camel($method);

        if (method_exists($this, $dinamic)) {
            return $this->{$dinamic}(...$parameters);
        }

        throw new \InvalidArgumentException("Method [{$method}] missing from ".get_class($this));
    }
}

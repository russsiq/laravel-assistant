<?php

namespace Russsiq\Assistant\Support;

use Artisan;

use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

use Russsiq\Assistant\Support\Contracts\CleanerContract;

class Cleaner implements CleanerContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр Оптимизатора.
     *
     * @param  Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
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
     * Очистка кэша настроек приложения.
     *
     * @return void
     */
    public function clearConfig()
    {
        // code...
    }

    /**
     * Очистка кэша маршрутов приложения.
     *
     * @return void
     */
    public function clearRoute()
    {
        // code...
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

    /**
     * Комплексная очистка.
     *
     * @return void
     */
    public function complexClear()
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
    protected function artisanCall(string $name): string
    {
        Artisan::call($name);

        return trim(Artisan::output());
    }

    public function proccess(array $methods)
    {
        $messages = [];

        foreach ($methods as $method) {
            $messages[] = $this->{$method}();
        }

        return $messages;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return string
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

<?php

namespace Russsiq\Assistant\Support\Contracts;

/**
 * @see Illuminate\Foundation\Console\OptimizeClearCommand;
 * @see Illuminate\Foundation\Console\OptimizeCommand;
 */
interface CleanerContract
{
    /**
     * Очистка кэша приложения.
     *
     * @return void
     */
    public function clearCache();

    /**
     * Кэширование конфигураций приложения.
     *
     * @return void
     */
    public function cacheConfig();

    /**
     * Очистка кэша конфигураций приложения.
     *
     * @return void
     */
    public function clearConfig();

    /**
     * Кэширование маршрутов приложения.
     *
     * @return void
     */
    public function cacheRoute();

    /**
     * Очистка кэша маршрутов приложения.
     *
     * @return void
     */
    public function clearRoute();

    /**
     * Очистка скомпилированных шаблонов приложения.
     *
     * @return void
     */
    public function clearView();

    /**
     * Комплексная очистка и последующее кэширование.
     *
     * @return void
     */
    public function complexOptimize();

    /**
     * Запустить внутренние методы очистки, кэширования, оптимизации.
     *
     * @param  array  $methods Массив методов.
     *
     * @return void
     */
    public function process(array $methods);
}

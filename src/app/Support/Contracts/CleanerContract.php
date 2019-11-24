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
     * Очистка кэша настроек приложения.
     *
     * @return void
     */
    public function clearConfig();

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
}

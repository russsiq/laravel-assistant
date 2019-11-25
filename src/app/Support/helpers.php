<?php

use Illuminate\Support\Str;

/**
 * anti_globals - Получить массив "зловредных" глобальных переменных.
 * server_requirements - Получить массив с набором минимальных системных требований к серверу.
 */

if (! function_exists('anti_globals')) {
    /**
    * Получить массив "зловредных" глобальных переменных.
    *
    * @return array
    */
    function anti_globals(): array
    {
        static $globals = null;

        if (is_null($globals)) {
            $config = config('assistant.installer.globals');

            foreach ($config as $key => $value) {
                $globals[$key] = $value === (bool) ini_get($key);
            }
        }

        return $globals;
    }
}

if (! function_exists('server_requirements')) {
    /**
     * Получить массив с набором минимальных системных требований к серверу.
     *
     * @return array
     */
    function server_requirements(): array
    {
        static $requirements = null;

        if (is_null($requirements)) {
            $config = config('assistant.installer.requirements');

            foreach ($config as $key => $value) {
                if (Str::startsWith($key, 'ext-')) {
                    $key = str_replace('ext-', '', $key);
                    $requirements[$key] = extension_loaded($key) && version_compare(phpversion($key), $value, '>=');
                } elseif (is_bool($value)) {
                    $requirements[$key] = $value;
                } elseif ('php' === $key) {
                    $requirements[$key] = version_compare(phpversion(), $value, '>=');
                }
            }
        }

        return $requirements;
    }
}

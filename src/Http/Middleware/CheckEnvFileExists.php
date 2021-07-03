<?php

namespace Russsiq\Assistant\Http\Middleware;

use Closure;
use Russsiq\Assistant\Facades\Installer;

/**
 * Проверка на то, что система является установленной.
 * В этой проверке также интересует физическое присутствие файла окружения.
 *
 * Нельзя использовать кэшированные `config('app.key')`,
 * т.к. неопределенное время назад была отмечена
 * какая-то несовместимость `\Dotenv` и `ajax` запросов.
 * В данный момент ничего об этом не известно.
 */
class CheckEnvFileExists
{
    protected $location;

    /**
     * Обработка входящего запроса.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $args = func_get_args();

        $this->location = $request->route()->getPrefix();

        if (Installer::alreadyInitiated()) {
            return $this->handleWithEnvFile(...$args);
        }

        return $this->handleWithoutEnvFile(...$args);
    }

    /**
     * Обработка входящего запроса, если файл окружения существует.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    protected function handleWithEnvFile($request, Closure $next)
    {
        // Если приложение не установлено:
        if (! Installer::alreadyInstalled()) {
            // Задаем необходимые для установки параметры конфигурации.
            config([
                'session.driver' => 'file', // Нельзя трогать БД.
            ]);

            // Если текущий маршрут – не маршрут установщика,
            // то перенаправляем на установку.
            if (! $this->isLocation('assistant/install')) {
                return redirect()->route('assistant.install.welcome');
            }
        }

        return $next($request);
    }

    /**
     * Обработка входящего запроса, если файл окружения не существует.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    protected function handleWithoutEnvFile($request, Closure $next)
    {
        Installer::initiate();

        // Перенаправляем на страницу установки.
        return redirect()->route('assistant.install.welcome');
    }

    /**
     * Получить текущий раздел маршрута.
     *
     * @return string|null
     */
    protected function location()
    {
        return $this->location;
    }

    /**
     * Проверить, что текущий раздел маршрута совпадает с переданным.
     *
     * @param  string  $path
     * @return bool
     */
    protected function isLocation(string $path): bool
    {
        return $path === $this->location();
    }
}

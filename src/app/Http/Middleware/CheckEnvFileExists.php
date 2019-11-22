<?php

namespace Russsiq\Assistant\Http\Middleware;

use Closure;
use LogicException;
use RuntimeException;

use Artisan;

use Russsiq\EnvManager\Support\Facades\EnvManager;

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
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $args = func_get_args();
        $this->location = $request->route()->getPrefix(); // $request->decodedPath();

        if (EnvManager::fileExists()) {
            return $this->handleWithEnvFile(...$args);
        }

        return $this->handleWithoutEnvFile(...$args);
    }

    /**
     * Обработка входящего запроса, если файл окружения существует.
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     *
     * @throws LogicException
     */
    public function handleWithEnvFile($request, Closure $next)
    {
        // Маркер, что приложение считается установленным.
        $installed = strtotime(EnvManager::get('APP_INSTALLED_AT'));

        // Если приложение не установлено и
        // текущий маршрут - не маршрут установщика,
        // то перенаправляем на установку.
        if (! $installed and ! $this->isLocation('assistant/install')) {
            return redirect()->route('assistant.install.welcome');
        }

        return $next($request);
    }

    /**
     * Обработка входящего запроса, если файл окружения не существует.
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handleWithoutEnvFile($request, Closure $next)
    {
        // Создаем новый файл из образца,
        // попутно генерируя ключ для приложения.
        EnvManager::newFromPath(base_path('.env.example'), true)
            // Устанавливаем необходимые значения.
            ->setMany([
                'APP_URL' => url('/'),
            ])
            // Сохраняем новый файл в корне как `.env`.
            ->save();

        // Очищаем ненужный хлам.
        $exit_code = Artisan::call('cache:clear');
        $exit_code = Artisan::call('config:clear');
        $exit_code = Artisan::call('route:clear');
        $exit_code = Artisan::call('view:clear');

        // Для запуска приложения необходимо задать минимальные параметры.
        config([
            'app.key' => EnvManager::get('APP_KEY')
        ]);

        // Перенаправляем на страницу установки.
        return redirect()->route('assistant.install.welcome');
    }

    /**
     * Получить текущий раздел маршрута.
     * @return string|null
     */
    protected function location()
    {
        return $this->location;
    }

    /**
     * Проверить, что текущий раздел маршрута совпадает с переданным.
     * @param  string  $path
     * @return bool
     */
    protected function isLocation(string $path): bool
    {
        return $path === $this->location();
    }
}

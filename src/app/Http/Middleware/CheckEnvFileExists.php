<?php

namespace Russsiq\Assistant\Http\Middleware;

use Closure;
use LogicException;
use RuntimeException;

use Illuminate\Encryption\Encrypter;

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
        $this->location = $request->decodedPath();

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

        // Если ключ приложения уже был создан и
        // приложение считается установленным,
        // но был запрошен маршрут установщика.
        if ($installed and $this->isLocation('assistant/install')) {
            throw new LogicException('File `.env` already exists! Delete it and continue.');
        }

        // Если приложение не установлено и
        // текущий маршрут - не маршрут установщика,
        // то перенаправляем на установку.
        if (! $installed and ! $this->isLocation('assistant/install')) {
            return redirect()
                ->route('assistant.install.step_choice');
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
        // Предварительно генерируем ключ приложения.
        $key = $this->generateRandomKey();

        // Для запуска приложения необходимо задать минимальные параметры.
        config([
            'app.key' => $request->APP_KEY ?? $key,
        ]);

        // Если в запросе не был передан ключ приложения или
        // текущий маршрут - не маршрут установщика,
        // то редирект на страницу установки с передачей ключа в запросе.
        if (! $request->APP_KEY or ! $this->isLocation('assistant/install')) {
            return redirect()
                ->route('assistant.install.step_choice', [
                    'APP_KEY' => $key,
                ]);
        }

        return $next($request);
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

    /**
     * Сгенерировать случайный ключ для приложения.
     * По мотивам: `Illuminate\Foundation\Console\KeyGenerateCommand`.
     * @return string
     */
    protected function generateRandomKey(): string
    {
        return 'base64:'.base64_encode(Encrypter::generateKey('AES-256-CBC'));
    }
}

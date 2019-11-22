<?php

namespace Russsiq\Assistant\Http\Middleware;

use Closure;
use EnvManager;

/**
 * Если существует дата установки приложения,
 * то приложение считается установленным,
 *
 * Исходя из этого - маршрут установщика должен быть блокирован.
 */
class AlreadyInstalled
{
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
        // Маркер, что приложение считается установленным.
        if (strtotime(EnvManager::get('APP_INSTALLED_AT'))) {
            return redirect('/')
                ->withErrors('File `.env` already exists! Delete it and continue.');
        }

        return $next($request);
    }
}

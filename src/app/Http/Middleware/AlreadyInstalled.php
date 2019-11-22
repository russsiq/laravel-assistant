<?php

namespace Russsiq\Assistant\Http\Middleware;

use Closure;

use Russsiq\EnvManager\Support\Facades\EnvManager;

/**
 * Если ключ приложения уже был создан и
 * приложение считается установленным,
 * но был запрошен маршрут установщика.
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

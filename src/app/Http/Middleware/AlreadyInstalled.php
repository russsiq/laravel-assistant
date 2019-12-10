<?php

namespace Russsiq\Assistant\Http\Middleware;

use Closure;
use Installer;

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
        if (Installer::alreadyInstalled()) {
            return redirect('/')
                ->withErrors(trans('assistant::install.messages.errors.already_installed'));
        }

        return $next($request);
    }
}

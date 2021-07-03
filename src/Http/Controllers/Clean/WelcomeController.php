<?php

namespace Russsiq\Assistant\Http\Controllers\Clean;

use Russsiq\Assistant\Facades\Cleaner;
use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Clean\CleanRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('clean.welcome');
    }

    public function store(CleanRequest $request)
    {
        // Отправляем в обработку Чистильщику
        // только ключи из запроса.
        Cleaner::process($request->keys());

        // Обратите внимание, что после кэширования маршрутов или кэширования конфигураций
        // становится невозможным передача/получение сообщений через сессии.
        // Таким образом, не отрабатывает метод `with`,
        // относящийся к классу `Illuminate\Http\RedirectResponse`.
        return redirect()->route('assistant.clean.complete');
    }

    public function complete()
    {
        return $this->makeResponse('clean.complete', [
            'messages_cache_key' => Cleaner::getMessagesCacheKey(),
        ]);
    }

    public function redirect()
    {
        return redirect()->route('assistant.clean.welcome');
    }
}

<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use EnvManager;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\WelcomeRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('install.welcome');
    }

    public function store(WelcomeRequest $request)
    {
        // Используем только те данные,
        // для которых описаны правила валидации.
        $data = $request->validated();

        // Save to `.env` file prev request from form
        EnvManager::setMany($data)->save();

        return redirect()->route('assistant.install.permission');
    }
}

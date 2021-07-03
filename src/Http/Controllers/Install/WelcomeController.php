<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\WelcomeRequest;
use Russsiq\EnvManager\Facades\EnvManager;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('install.welcome', [
            'selecting_environments' => [
                'local',
                'dev',
                'testing',
                'production',
            ],
        ]);
    }

    public function store(WelcomeRequest $request)
    {
        EnvManager::setMany($request->validated())->save();

        return redirect()->route('assistant.install.permission');
    }
}

<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use EnvManager;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\MigrateRequest;

class MigrateController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('install.migrate');
    }

    public function store(MigrateRequest $request)
    {
        // Save to `.env` file prev request from form
        EnvManager::setMany($request->all())->save();

        return redirect()->route('assistant.install.common');
    }
}

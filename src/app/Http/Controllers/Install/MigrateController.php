<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Artisan;

use Russsiq\Assistant\Http\Requests\Install\MigrateRequest;
use Russsiq\EnvManager\Support\Facades\EnvManager;

class MigrateController extends Controller
{
    public function index()
    {
        return $this->makeResponse('migrate', $this->vars);
    }

    public function store(MigrateRequest $request)
    {
        // Save to `.env` file prev request from form
        EnvManager::setMany($request->all())->save();

        return redirect()->route('assistant.install.common');
    }
}

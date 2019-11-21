<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Artisan;

use Russsiq\Assistant\Http\Requests\Install\DatabaseRequest;
use Russsiq\EnvManager\Support\Facades\EnvManager;

class DatabaseController extends Controller
{
    public function index()
    {
        return $this->makeResponse('database', $this->vars);
    }

    public function store(DatabaseRequest $request)
    {
        // Save to `.env` file prev request from form
        EnvManager::setMany($request->all())->save();

        return redirect()->route('assistant.install.migrate');
    }
}

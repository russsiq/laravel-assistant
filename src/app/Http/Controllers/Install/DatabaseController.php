<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use EnvManager;
use Installer;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\DatabaseRequest;

class DatabaseController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('install.database');
    }

    public function store(DatabaseRequest $request)
    {
        try {
            Installer::checkConnection($request->validated());

            $message = Installer::migrate();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'database' => $e->getMessage()
                ]);
        }

        // Save to `.env` file prev request from form
        EnvManager::setMany($request->all())->save();

        return redirect()
            ->route('assistant.install.migrate')
            ->withStatus($message);
    }
}

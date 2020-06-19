<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use EnvManager;
use Installer;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\PermissionRequest;

class PermissionController extends BaseController
{
    public function index()
    {
        $requirements = Installer::requirements();
        $globals = Installer::antiGlobals();
        $permissions = Installer::filePermissions();

        return $this->makeResponse('install.permission', compact(
            'requirements',
            'globals',
            'permissions'
        ));
    }

    public function store(PermissionRequest $request)
    {
        // Save to `.env` file prev request from form
        EnvManager::setMany($request->all())->save();

        return redirect()->route('assistant.install.database');
    }
}

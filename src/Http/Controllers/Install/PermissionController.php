<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Russsiq\Assistant\Facades\Installer;
use Russsiq\Assistant\Http\Controllers\BaseController;

class PermissionController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('install.permission', [
            'requirements' => Installer::requirements(),
            'globals' => Installer::antiGlobals(),
            'permissions' => Installer::filePermissions(),
        ]);
    }

    public function store()
    {
        return redirect()->route('assistant.install.database');
    }
}

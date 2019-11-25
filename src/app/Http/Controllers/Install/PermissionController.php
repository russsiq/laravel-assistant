<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use EnvManager;
use Installer;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\PermissionRequest;

class PermissionController extends BaseController
{
    protected $chmod = [
        'bootstrap/cache/',
        'config/',
        'config/settings/',
        'storage/app/backups/',
        'storage/app/uploads/'
    ];

    public function index()
    {
        $requirements = Installer::requirements();
        $globals = Installer::antiGlobals();

        $chmod = collect($this->chmod)
            ->mapWithKeys(function ($item) {

                clearstatcache(true, $path = app()->basePath($item));

                return [
                    $item => (object) [
                        'perm' => ((file_exists($path) and $x = fileperms($path)) === false) ? null : (decoct($x) % 1000),
                        'status' => is_writable($path) ?? null,
                    ]
                ];
            })->all();

        return $this->makeResponse('install.permission', compact('requirements', 'globals', 'chmod'));
    }

    public function store(PermissionRequest $request)
    {
        // Save to `.env` file prev request from form
        EnvManager::setMany($request->all())->save();

        return redirect()->route('assistant.install.database');
    }
}

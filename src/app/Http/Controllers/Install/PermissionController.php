<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use EnvManager;

use Russsiq\Assistant\Http\Requests\Install\PermissionRequest;

class PermissionController extends Controller
{
    protected $minreq = [
        'php',
        'pdo',
        'ssl',
        'gd',
        'finfo',
        'mb',
        'tokenizer',
        'ctype',
        'json',
        'xml',
        'zlib',
    ];

    protected $antiGlobals = [
        'register_globals',
        'magic_quotes_gpc',
        'magic_quotes_runtime',
        'magic_quotes_sybase',
    ];

    protected $chmod = [
        'bootstrap/cache/',
        'config/',
        'config/settings/',
        'storage/app/backups/',
        'storage/app/uploads/'
    ];

    public function index()
    {
        $minreq = collect($this->minreq)
            ->mapWithKeys(function ($item) {
                return [
                    $item => minreq($item),
                ];
            })->all();

        $globals = collect($this->antiGlobals)
            ->mapWithKeys(function ($item) {
                return [
                    $item => ini_get($item),
                ];
            })->all();

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

        return $this->makeResponse('permission', array_merge(
            $this->vars, compact('minreq', 'globals', 'chmod')
        ));
    }

    public function store(PermissionRequest $request)
    {
        // Save to `.env` file prev request from form
        EnvManager::setMany($request->all())->save();

        return redirect()->route('assistant.install.database');
    }
}

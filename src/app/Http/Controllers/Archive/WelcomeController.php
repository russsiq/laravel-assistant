<?php

namespace Russsiq\Assistant\Http\Controllers\Archive;

use EnvManager;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Archive\WelcomeRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('archive.welcome');
    }

    public function store(WelcomeRequest $request)
    {
        // return redirect()->route('assistant.archive.permission');
    }
}

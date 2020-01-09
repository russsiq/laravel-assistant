<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\WelcomeRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('install.welcome');
    }

    public function store(WelcomeRequest $request)
    {
        return redirect()->route('assistant.install.permission');
    }
}

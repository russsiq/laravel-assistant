<?php

namespace Russsiq\Assistant\Http\Controllers\Clean;

use EnvManager;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Clean\WelcomeRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('clean.welcome');
    }

    public function store(WelcomeRequest $request)
    {
        // return redirect()->route('assistant.clean.permission');
    }
}

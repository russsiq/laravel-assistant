<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Russsiq\Assistant\Http\Requests\Install\WelcomeRequest;

class WelcomeController extends Controller
{
    public function index()
    {
        return $this->makeResponse('welcome', $this->vars);
    }

    public function store(WelcomeRequest $request)
    {
        return redirect()->route('assistant.install.permission');
    }
}

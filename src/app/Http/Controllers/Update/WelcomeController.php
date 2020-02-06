<?php

namespace Russsiq\Assistant\Http\Controllers\Update;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Update\UpdateRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('update.welcome');
    }

    public function store(UpdateRequest $request)
    {
        // return redirect()->route('assistant.update.permission');
    }
}

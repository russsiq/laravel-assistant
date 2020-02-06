<?php

namespace Russsiq\Assistant\Http\Controllers\Update;

use Updater;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Update\UpdateRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('update.welcome', [
            //
        ]);
    }

    public function store(UpdateRequest $request)
    {
        return redirect()->route('assistant.update.complete');
    }

    public function complete()
    {
        return $this->makeResponse('update.complete', [
            //
        ]);
    }

    public function redirect()
    {
        return redirect()->route('assistant.update.welcome');
    }
}

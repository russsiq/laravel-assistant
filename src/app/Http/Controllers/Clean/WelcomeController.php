<?php

namespace Russsiq\Assistant\Http\Controllers\Clean;

use Cleaner;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Clean\CleanRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('clean.welcome');
    }

    public function store(CleanRequest $request)
    {
        $messages = Cleaner::proccess(
            array_keys($request->all())
        );

        return redirect()
            ->route('assistant.clean.complete')
            ->with(compact('messages'));
    }

    public function complete()
    {
        return $this->makeResponse('clean.complete');
    }

    public function redirect()
    {
        return redirect()->route('assistant.clean.welcome');
    }
}

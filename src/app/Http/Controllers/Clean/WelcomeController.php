<?php

namespace Russsiq\Assistant\Http\Controllers\Clean;

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
        $messages = [
            'clean' => 'Выполнено',
            'cache' => 'Выполнено',
            'optimize' => 'Выполнено',

        ];

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

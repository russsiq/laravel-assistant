<?php

namespace Russsiq\Assistant\Http\Controllers\Archive;

// Сторонние зависимости.
use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Archive\ArchiveRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('archive.welcome', [

        ]);
    }

    public function store(ArchiveRequest $request)
    {
        return redirect()->route('assistant.archive.complete')->with([
            'status' => 'success',

        ]);
    }

    public function complete()
    {
        return $this->makeResponse('archive.complete', [

        ]);
    }

    public function redirect()
    {
        return redirect()->route('assistant.archive.welcome');
    }
}

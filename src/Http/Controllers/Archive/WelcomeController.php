<?php

namespace Russsiq\Assistant\Http\Controllers\Archive;

// Зарегистрированные фасады приложения.
use Russsiq\Assistant\Facades\Archivist;

// Сторонние зависимости.
use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Archive\ArchiveRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('archive.welcome', [
            'operator' => Archivist::KEY_NAME_OPERATOR,
            'backups' => Archivist::backups(),

        ]);
    }

    public function store(ArchiveRequest $request)
    {
        $action = $request->get(Archivist::KEY_NAME_OPERATOR);

        return redirect()->route('assistant.archive.complete')->with([
            'status' => 'success',
            // 'result' => Archivist::process($request->all()),
            'messages' => Archivist::operator($action)
                ->setOptions($request->all())
                ->execute(),

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

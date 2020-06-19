<?php

namespace Russsiq\Assistant\Http\Controllers\Update;

use Updater;

use Carbon\Carbon;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Update\UpdateRequest;

class WelcomeController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('update.welcome', [
            'available_version' => Updater::availableVersion(),
            'currently_version' => Updater::currentlyVersion(),
            'is_new_version_available' => Updater::isNewVersionAvailable(),
            'installed_at' => Carbon::parse(Updater::installedAt())
                ->isoFormat('LLLL'),

        ]);
    }

    public function store(UpdateRequest $request)
    {
        if (Updater::isNewVersionAvailable()) {
            Updater::fetch();
            Updater::update();
        }

        return redirect()->route('assistant.update.complete')->with([
            'status' => 'success',

        ]);
    }

    public function complete()
    {
        return $this->makeResponse('update.complete', [
            'currently_version' => Updater::currentlyVersion(),
        ]);
    }

    public function redirect()
    {
        return redirect()->route('assistant.update.welcome');
    }
}

<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use EnvManager;
use Installer;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\DatabaseRequest;

class DatabaseController extends BaseController
{
    public function index()
    {
        return $this->makeResponse('install.database');
    }

    public function store(DatabaseRequest $request)
    {
        $data = $request->validated();

        try {
            Installer::checkConnection($data);

            $messages = [
                'migrate' => Installer::migrate(),
                'seeds' => [
                    //
                ],

            ];

            Installer::when(
                config('assistant.installer.seeds.database', false),
                function ($installer) use (&$messages) {
                    $messages['seeds'][] = $installer->seed(
                        config('assistant.installer.seeds.database')
                    );
                }
            );

            Installer::when(
                $data['test_seed'] && config('assistant.installer.seeds.test', false),
                function ($installer) use (&$messages) {
                    $messages['seeds'][] = $installer->seed(
                        config('assistant.installer.seeds.test', false)
                    );
                }
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'database' => $e->getMessage()
                ]);
        }

        // Save to `.env` file prev request from form
        EnvManager::setMany($data)->save();

        return redirect()
            ->route('assistant.install.database-complete')
            ->with(compact('messages'));
    }

    public function complete()
    {
        return $this->makeResponse('install.database-complete');
    }

    public function redirect()
    {
        return redirect()->route('assistant.install.common');
    }
}

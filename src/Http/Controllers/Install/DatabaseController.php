<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Russsiq\Assistant\Facades\Installer;
use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\DatabaseRequest;
use Russsiq\EnvManager\Facades\EnvManager;

class DatabaseController extends BaseController
{
    public function index(Application $app)
    {
        return $this->makeResponse('install.database', [
            'isProductionEnvironment' => $app->environment('production')
        ]);
    }

    public function store(DatabaseRequest $request)
    {
        // Используем только те данные,
        // для которых описаны правила валидации.
        $data = $request->validated();

        try {
            // Выполнить проверку подключения к БД.
            Installer::checkConnection($data);

            $messages = [
                // Применить миграции.
                'migrate' => Installer::migrate(),
                'seeds' => [
                    //
                ],
            ];

            // Наполнить БД начальными данными.
            Installer::when(
                config('assistant.installer.seeds.database', false),
                function ($installer) use (&$messages) {
                    $messages['seeds'][] = $installer->seed(
                        config('assistant.installer.seeds.database')
                    );
                }
            );

            // Наполнить БД фиктивными данными.
            Installer::when(
                $data['test_seed'] && config('assistant.installer.seeds.test', false),
                function ($installer) use (&$messages) {
                    $messages['seeds'][] = $installer->seed(
                        config('assistant.installer.seeds.test', false)
                    );
                }
            );
        } catch (Exception $e) {
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

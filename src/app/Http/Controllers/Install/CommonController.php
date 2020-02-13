<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Artisan;
use EnvManager;
use Installer;

use Throwable;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\CommonRequest;

use Illuminate\Validation\ValidationException;

class CommonController extends BaseController
{
    /**
     * Индикатор, что данная стадия мастера завершающая.
     * @var boolean
     */
    protected $onFinishStage = true;

    public function index()
    {
        return $this->makeResponse('install.common', [
            'APP_URL' => url('/'),
            'email' => 'admin@'.request()->getHttpHost(),
        ]);
    }

    public function store(CommonRequest $request)
    {
        $data = $request->all();

        try {
            // Выполняем копирование необходимх директорий.
            Installer::copyDirectories();

            // Создаем ссылки на необходимые директории.
            Installer::createSymbolicLinks();

            $response = Installer::beforeInstalled($request);

        } catch (ValidationException $ex) {

            throw $ex;
        } catch (Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'common' => $e->getMessage()
                ]);
        }

        // Finaly set to `.env` variables
        EnvManager::setMany(array_merge($data, [
                // Теперь система будет считаться установленной.
                'APP_INSTALLED_AT' => date('Y-m-d H:i:s'),

            ]))
            ->save();

        // Очищаем ненужный хлам.
        $exit_code = Artisan::call('cache:clear');
        $exit_code = Artisan::call('config:clear');
        $exit_code = Artisan::call('route:clear');
        $exit_code = Artisan::call('view:clear');

        return $response;
    }
}

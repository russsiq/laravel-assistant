<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Artisan;
use EnvManager;
use Installer;

use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\CommonRequest;

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
            'themes' => collect(select_dir('themes'))->map('theme_version')->filter(),
        ]);
    }

    public function store(CommonRequest $request)
    {
        $data = $request->all();

        try {
            Installer::registerOwner($data);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'database' => $e->getMessage()
                ]);
        }

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

        return redirect('/');
    }
}

<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Artisan;
use EnvManager;

use Russsiq\Assistant\Http\Requests\Install\CommonRequest;

class CommonController extends Controller
{
    public function index()
    {
        return $this->makeResponse('common', array_merge($this->vars, [
            'APP_URL' => url('/'),
            'email' => 'admin@'.request()->getHttpHost(),
            'themes' => collect(select_dir('themes'))->map('theme_version')->filter(),
        ]));
    }

    public function store(CommonRequest $request)
    {
        // Save to `.env` file prev request from form
        EnvManager::setMany($request->all())->save();

        // Очищаем ненужный хлам.
        $exit_code = Artisan::call('cache:clear');
        $exit_code = Artisan::call('config:clear');
        $exit_code = Artisan::call('route:clear');
        $exit_code = Artisan::call('view:clear');

        return redirect('/');
    }
}

<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Illuminate\Validation\ValidationException;
use Russsiq\Assistant\Facades\Cleaner;
use Russsiq\Assistant\Facades\Installer;
use Russsiq\Assistant\Http\Controllers\BaseController;
use Russsiq\Assistant\Http\Requests\Install\CommonRequest;
use Russsiq\EnvManager\Facades\EnvManager;
use Throwable;

class CommonController extends BaseController
{
    /**
     * Индикатор, что данная стадия мастера завершающая.
     *
     * @var bool
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
        try {
            // Выполняем копирование необходимых директорий.
            Installer::copyDirectories();

            // Создаем ссылки на необходимые директории.
            Installer::createSymbolicLinks();

            // Получаем ответ от посредника.
            $response = Installer::beforeInstalled($request);
        } catch (ValidationException $ex) {
            throw $ex;
        } catch (Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'common' => $e->getMessage(),
                ]);
        }

        // Устанавливаем оставшиеся переменные в файл `.env`.
        EnvManager::setMany(array_merge($request->all(), [
            // Теперь система будет считаться установленной.
            'APP_INSTALLED_AT' => date('Y-m-d H:i:s'),
        ]))
            ->save();

        // Очищаем ненужный хлам.
        Cleaner::process([
            'clear_cache',
            'clear_config',
            'clear_route',
            'clear_view',
        ]);

        return $response;
    }
}

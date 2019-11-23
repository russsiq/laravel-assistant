<?php

namespace Russsiq\Assistant\Http\Controllers;

use EnvManager;
use Route;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Префикс шаблона.
     * @var string
     */
    protected $templatePrefix = 'assistant::';

    /**
     * Индикатор, что данная стадия мастера завершающая.
     * @var boolean
     */
    protected $onFinishStage = false;

    /**
     * Массив переменных для шаблона.
     * @var array
     */
    protected $variables = [
        //
    ];

    public function __construct()
    {
        $this->fillableVariables();
    }

    /**
     * Заполнить массив переменных начальными данными.
     *
     * @return void
     */
    protected function fillableVariables()
    {
        $this->variables = array_merge($this->variables, [
            'installed' => strtotime(EnvManager::get('APP_INSTALLED_AT')),
            'action' => $this->getCurrentAction(),
            'master' => $this->getCurrentMaster(),
            'stage' => $this->getCurrentStage(),
            'onFinishStage' => $this->onFinishStage(),
        ]);
    }

    /**
     * Получить имя текущего маршрута.
     *
     * @return string
     */
    protected function routeName(): string
    {
        return Route::currentRouteName();
    }

    /**
     * Получить значение текущущего действия
     * для атрибута `action` формы.
     *
     * @return string
     */
    protected function getCurrentAction(): string
    {
        return route($this->routeName());
    }

    /**
     *
     *
     * @return array
     */
    protected function getRoutePart(int $part): string
    {
        static $parts = null;

        if (is_null($parts)) {
            $parts = explode('.', $this->routeName());
        }

        return $parts[$part];
    }

    /**
     * Получить название текущущего мастера.
     *
     * @return string
     */
    protected function getCurrentMaster(): string
    {
        return $this->getRoutePart(1);
    }

    /**
     * Получить название текущущей стадии.
     *
     * @return string
     */
    protected function getCurrentStage(): string
    {
        return $this->getRoutePart(2);
    }

    /**
     * Определить, является ли текущий экран мастера завершающим.
     *
     * @return string
     */
    protected function onFinishStage(): bool
    {
        return $this->onFinishStage;
    }

    /**
     * Получить содержимое шаблона для формирования HTTP ответа.
     *
     * @param  string $template  Имя шаблона
     * @param  array  $variables Массив переменных шаблона.
     *
     * @return mixed
     */
    public function makeResponse(string $template, array $variables = [])
    {
        $template = $this->templatePrefix.$template;

        if (view()->exists($template)) {
            return view($template, $this->variables, $variables)->render();
        }

        abort(404, "View named [$template] not exists.");
    }
}

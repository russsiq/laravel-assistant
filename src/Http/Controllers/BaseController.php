<?php

namespace Russsiq\Assistant\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Russsiq\EnvManager\Facades\EnvManager;

abstract class BaseController extends Controller
{
    /**
     * Префикс шаблона.
     *
     * @var string
     */
    protected $templatePrefix = 'assistant::';

    /**
     * Индикатор, что данная стадия мастера завершающая.
     *
     * @var bool
     */
    protected $onFinishStage = false;

    /**
     * Массив переменных для шаблона.
     *
     * @var array
     */
    protected $variables = [
        //
    ];

    /**
     * Заполнить массив переменных начальными данными.
     *
     * @return void
     */
    protected function fillVariables(): void
    {
        $this->variables = array_merge([
            'installed' => strtotime(EnvManager::get('APP_INSTALLED_AT')),
            'action' => $this->getCurrentAction(),
            'master' => $this->getCurrentMaster(),
            'stage' => $this->getCurrentStage(),
            'onFinishStage' => $this->onFinishStage(),
        ], $this->variables);
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
     * Получить значение текущего действия
     * для атрибута `action` формы.
     *
     * @return string
     */
    protected function getCurrentAction(): string
    {
        return route($this->routeName());
    }

    /**
     * Get route part.
     *
     * @return string
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
     * Получить название текущего мастера.
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
     * @return mixed
     */
    public function makeResponse(string $template, array $variables = [])
    {
        $this->fillVariables();

        $template = $this->templatePrefix.$template;

        if (view()->exists($template)) {
            return view($template, $this->variables, $variables)->render();
        }

        abort(404, "View named [$template] not exists.");
    }
}

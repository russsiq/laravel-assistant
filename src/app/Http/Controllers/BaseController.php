<?php

namespace Russsiq\Assistant\Http\Controllers;

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
     * Индикатор, что данный экран мастера завершающий.
     * @var boolean
     */
    protected $onFinishScreen = false;

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
            'action' => $this->getCurrentAction(),
            'section' => $this->getCurrentSection(),
            'onFinishScreen' => $this->onFinishScreen(),
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
     * Получить значение текущущего раздела
     * для формирования заголовка.
     *
     * @return string
     */
    protected function getCurrentSection(): string
    {
        $parts = explode('.', $this->routeName());

        return end($parts);
    }

    /**
     * Определить, является ли текущий экран мастера завершающим.
     *
     * @return string
     */
    protected function onFinishScreen(): bool
    {
        return $this->onFinishScreen;
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

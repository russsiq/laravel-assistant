<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Russsiq\Assistant\Http\Controllers\BaseController;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

abstract class Controller extends BaseController
{
    protected $routeName;

    protected $onFinishScreen = false;

    protected $template = 'install';

    protected $vars = [
        //
    ];

    public function __construct(array $vars = [])
    {
        parent::__construct();

        $this->routeName = Route::currentRouteName();

        $this->fillable($vars);
    }

    protected function fillable(array $vars)
    {
        $this->vars = array_merge([
            'action' => $this->getCurrentAction(),
            'section' => $this->getCurrentSection(),
            'onFinishScreen' => $this->onFinishScreen(),
        ], $this->vars, $vars);
    }

    protected function getCurrentAction(): string
    {
        return route($this->routeName);
    }

    protected function getCurrentSection(): string
    {
        $parts = explode('.', $this->routeName);

        return end($parts);
    }

    protected function onFinishScreen(): bool
    {
        return $this->onFinishScreen;
    }
}

<?php

namespace Russsiq\Assistant\Http\Controllers\Install;

use Russsiq\Assistant\Http\Controllers\BaseController;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

abstract class Controller extends BaseController
{
    protected $routeName;

    protected $curstep;

    /**
     * [protected description]
     * // don't delete `finish` step.
     * @var array
     */
    protected $steps = [
        1 => 'welcome',
        2 => 'permission',
        3 => 'database',
        4 => 'migrate',
        5 => 'common',
        6 => 'finish',
    ];

    protected $template = 'install';

    protected $vars = [
        //
    ];

    public function __construct(array $vars = [])
    {
        parent::__construct();

        $this->routeName = Route::currentRouteName();
        $this->curstep = $this->getCurrentStep($this->routeName);

        $this->fillable($vars);
    }

    protected function fillable(array $vars)
    {
        $count = count($this->steps);

        $this->vars = array_merge([
            'action' => route($this->routeName),
            'steps' => $this->steps,
            'count_steps' => $count,
            'cur_step' => $this->curstep,

            'onFirstStep' => $this->curstep === 1,
            'onFinishStep' => $this->curstep === ($count - 1),
            'onLastStep' => $this->curstep === $count,
        ], $this->vars, $vars);
    }

    protected function getCurrentStepName(string $name): string
    {
        $parts = explode('.', $name);

        return end($parts);
    }

    protected function getCurrentStep(string $name): int
    {
        return array_search($this->getCurrentStepName($name), $this->steps);
    }
}

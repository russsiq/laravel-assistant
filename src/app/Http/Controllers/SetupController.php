<?php

namespace Russsiq\Assistant\Http\Controllers;

use Russsiq\Assistant\Http\Controllers\Controller;

class SetupController extends Controller
{
    protected $template = 'setup';
    protected $templatePrefix = 'assistant::';

    public function __construct()
    {
        //
    }

    public function makeResponse(string $template, array $vars = [])
    {
        $tpl = $this->templatePrefix.$this->template . '.'. $template;

        if (view()->exists($tpl)) {
            return view($tpl, $vars)->render();
        }

        abort(404, "View named [$tpl] not exists.");
    }
}

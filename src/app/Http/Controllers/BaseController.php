<?php

namespace Russsiq\Assistant\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $template = 'setup';
    protected $templatePrefix = 'assistant::';

    public function __construct()
    {
        //
    }

    public function makeResponse(string $template, array $vars = [])
    {
        $template = $this->templatePrefix.$this->template.'.'.$template;

        if (view()->exists($template)) {
            return view($template, $vars)->render();
        }

        abort(404, "View named [$template] not exists.");
    }
}

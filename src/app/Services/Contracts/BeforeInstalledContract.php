<?php

namespace Russsiq\Assistant\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface BeforeInstalledContract
{
    /**
     * Обработка входящего запроса.
     *
     * @param  Request $request
     *
     * @return RedirectResponse
     */
    public function handle(Request $request): RedirectResponse;
}

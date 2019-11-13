<?php

Route::prefix('assistant')
    ->namespace('Russsiq\Assistant\Http\Controllers')
    ->middleware([
        'web',
        'debugbar.disable'
    ])
    ->group(function () {
        // dd('assistant');
        // // Одностраничная административная панель.
        // Route::middleware([
        //         'auth',
        //         'can:global.panel',
        //     ])
        //     ->get('/{any?}', 'PanelController')
        //     ->name('panel');

        Route::get('/install', [
            'uses' => 'SystemInstall@stepСhoice',
            'as' => 'assistant.install.step_choice',
            'middleware' => [
                //
            ],
        ]);

        Route::post('/install', [
            'uses' => 'SystemInstall@stepСhoice',
            'as' => 'assistant.install.step_choice',
            'middleware' => [
                //
            ],
        ]);
    });

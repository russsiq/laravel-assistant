<?php

Route::prefix('assistant')
    ->namespace('Russsiq\Assistant\Http\Controllers')
    ->middleware([
        'web',
    ])
    ->group(function () {
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

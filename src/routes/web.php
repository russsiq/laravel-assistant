<?php

Route::prefix('assistant')
    ->namespace('Russsiq\Assistant\Http\Controllers')
    ->middleware([
        'web',
    ])
    ->group(function () {
        Route::prefix('archive')
            ->namespace('Archive')
            ->middleware([
                //
            ])
            ->group(function () {
                // 1. Welcome
                Route::get('/', 'WelcomeController@index')->name('assistant.archive.welcome');
                Route::post('/', 'WelcomeController@store')->name('assistant.archive.welcome.store');
            });

        Route::prefix('clean')
            ->namespace('Clean')
            ->middleware([
                //
            ])
            ->group(function () {
                // 1. Welcome
                Route::get('/', 'WelcomeController@index')->name('assistant.clean.welcome');
                Route::post('/', 'WelcomeController@store')->name('assistant.clean.welcome.store');
            });

        Route::prefix('install')
            ->namespace('Install')
            ->middleware([
                'already-installed',
            ])
            ->group(function () {
                // 1. Welcome
                Route::get('/', 'WelcomeController@index')->name('assistant.install.welcome');
                Route::post('/', 'WelcomeController@store')->name('assistant.install.welcome.store');

                // 2. Permission
                Route::get('/permission', 'PermissionController@index')->name('assistant.install.permission');
                Route::post('/permission', 'PermissionController@store')->name('assistant.install.permission.store');

                // 3. Database
                Route::get('/database', 'DatabaseController@index')->name('assistant.install.database');
                Route::post('/database', 'DatabaseController@store')->name('assistant.install.database.store');

                // 4. Migrate
                Route::get('/migrate', 'MigrateController@index')->name('assistant.install.migrate');
                Route::post('/migrate', 'MigrateController@store')->name('assistant.install.migrate.store');

                // 5. Common
                Route::get('/common', 'CommonController@index')->name('assistant.install.common');
                Route::post('/common', 'CommonController@store')->name('assistant.install.common.store');
            });

        Route::prefix('update')
            ->namespace('Update')
            ->middleware([
                //
            ])
            ->group(function () {
                // 1. Welcome
                Route::get('/', 'WelcomeController@index')->name('assistant.update.welcome');
                Route::post('/', 'WelcomeController@store')->name('assistant.update.welcome.store');
            });
    });

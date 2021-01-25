<?php

use Illuminate\Support\Facades\Route;
use Russsiq\Assistant\Http\Controllers\Archive\WelcomeController as ArchiveWelcomeController;
use Russsiq\Assistant\Http\Controllers\Clean\WelcomeController as CleanWelcomeController;
use Russsiq\Assistant\Http\Controllers\Install\DatabaseController as InstallDatabaseController;
use Russsiq\Assistant\Http\Controllers\Install\CommonController as InstallCommonController;
use Russsiq\Assistant\Http\Controllers\Install\PermissionController as InstallPermissionController;
use Russsiq\Assistant\Http\Controllers\Install\WelcomeController as InstallWelcomeController;
use Russsiq\Assistant\Http\Controllers\Update\WelcomeController as UpdateWelcomeController;

/**
 * Описание посредников:
 *  `can:use-assistant` - текущий пользователь имеет право воспользоваться Ассистентом приложения.
 *  `already-installed` - проверить, что приложение установлено.
 */

Route::prefix('assistant')
    ->middleware([
        'web',
    ])->group(function () {
        Route::prefix('archive')
            ->middleware([
                'can:use-assistant',
            ])
            ->group(function () {
                // 1. Welcome
                Route::get('/', [ArchiveWelcomeController::class, 'index'])->name('assistant.archive.welcome');
                Route::post('/', [ArchiveWelcomeController::class, 'store'])->name('assistant.archive.welcome.store');

                // 2. Сomplete
                Route::get('/complete', [ArchiveWelcomeController::class, 'complete'])->name('assistant.archive.complete');
                Route::post('/complete', [ArchiveWelcomeController::class, 'redirect'])->name('assistant.archive.redirect');
            });

        Route::prefix('clean')
            ->middleware([
                'can:use-assistant',
            ])
            ->group(function () {
                // 1. Welcome
                Route::get('/', [CleanWelcomeController::class, 'index'])->name('assistant.clean.welcome');
                Route::post('/', [CleanWelcomeController::class, 'store'])->name('assistant.clean.welcome.store');

                // 2. Сomplete
                Route::get('/complete', [CleanWelcomeController::class, 'complete'])->name('assistant.clean.complete');
                Route::post('/complete', [CleanWelcomeController::class, 'redirect'])->name('assistant.clean.redirect');
            });

        Route::prefix('install')
            ->middleware([
                'already-installed',
            ])
            ->group(function () {
                // 1. Welcome
                Route::get('/', [InstallWelcomeController::class, 'index'])->name('assistant.install.welcome');
                Route::post('/', [InstallWelcomeController::class, 'store'])->name('assistant.install.welcome.store');

                // 2. Permission
                Route::get('/permission', [InstallPermissionController::class, 'index'])->name('assistant.install.permission');
                Route::post('/permission', [InstallPermissionController::class, 'store'])->name('assistant.install.permission.store');

                // 3. Database
                Route::get('/database', [InstallDatabaseController::class, 'index'])->name('assistant.install.database');
                Route::post('/database', [InstallDatabaseController::class, 'store'])->name('assistant.install.database.store');

                // 4. Database complete
                Route::get('/database-complete', [InstallDatabaseController::class, 'complete'])->name('assistant.install.database-complete');
                Route::post('/database-complete', [InstallDatabaseController::class,'redirect'])->name('assistant.install.database-redirect');

                // 5. Common
                Route::get('/common', [InstallCommonController::class, 'index'])->name('assistant.install.common');
                Route::post('/common', [InstallCommonController::class, 'store'])->name('assistant.install.common.store');
            });

        Route::prefix('update')
            ->middleware([
                'can:use-assistant',
            ])
            ->group(function () {
                // 1. Welcome
                Route::get('/', [UpdateWelcomeController::class, 'index'])->name('assistant.update.welcome');
                Route::post('/', [UpdateWelcomeController::class, 'store'])->name('assistant.update.welcome.store');

                // 2. Сomplete
                Route::get('/complete', [UpdateWelcomeController::class, 'complete'])->name('assistant.update.complete');
                Route::post('/complete', [UpdateWelcomeController::class, 'redirect'])->name('assistant.update.redirect');
            });
    });

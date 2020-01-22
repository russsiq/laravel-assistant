<?php

return [
    // Логирование событий.
    'log_events' => env('ASSISTANT_LOG_EVENTS', false),

    // Настройки почтовых уведомлений о событиях.
    'mail' => [
        //
    ],

    // Настройки Архивариуса.
    'archivist' => [
        //
    ],

    // Настройки Чистильщика.
    'cleaner' => [
        //
    ],

    // Настройки Установщика.
    'installer' => [
        'requirements' => [
            'php' => '7.2.0',
            'ext-bcmath' => '*',
            'ext-ctype' => '*',
            'ext-curl' => '*',
            'ext-gd' => '*',
            'ext-json' => '1.6.0',
            'ext-mbstring' => '*',
            // 'ext-memcached' => '*',
            'ext-openssl' => '*',
            // 'ext-pcntl' => '*',
            'ext-pdo' => '*',
            // 'ext-posix' => '*',
            // 'ext-redis' => '*',
            'ext-tokenizer' => '*',
            'ext-xml' => '*',
            'ext-zip' => '1.15.2',
            'ext-zlib' => '*',

            // В качесте образца.
            'fileinfo' => function_exists('finfo_open'),

        ],

        'globals' => [
            'magic_quotes_gpc' => false,
            'magic_quotes_runtime' => false,
            'magic_quotes_sybase' => false,
            'register_globals' => false,

        ],

        'permissions' => [
            'bootstrap/cache',
            'config',
            'config/settings',
            'storage/app/backups',
            'storage/app/uploads',

        ],

        'seeds' => [
            'database' => 'DatabaseSeeder',
            'test' => 'TestContentSeeder',

        ],

        // Копирование директорий: fromDir, toDir.
        'directories' => [
            // 'fromDir' => 'toDir',

        ],

        // Создание ссылок на директории: target => link.
        'symlinks' => [
            storage_path('app/public') => public_path('storage'),

        ],

        'before-installed' => '\\Russsiq\\Assistant\\Services\\BeforeInstalled',

    ],

    // Настройки Мастера обновлений.
    'updater' => [
        'download_path' => env('ASSISTANT_DOWNLOAD_PATH', storage_path('tmp')),
        'repository_url' => 'https://api.github.com/repos/<vendor>/<name>/releases/latest',
        'version_format' => env('ASSISTANT_VERSION_FORMAT', 'app_name-v__VERSION__.zip'),

    ],

];

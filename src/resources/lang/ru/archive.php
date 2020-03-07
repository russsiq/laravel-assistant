<?php

return [
    'headers' => [
        'welcome' => 'Приветствие',
        'complete' => 'Выполнено',

    ],

    'descriptions' => [
        'welcome' => '<p>Данный мастер поможет в управлении резервными копиями приложения.</p><p>Выберите необходимые опции и нажмите <kbd>Далее</kbd>.</p>',
        'complete' => '<p>Завершена работа по выбранным вами опциям:</p>',

    ],

    'strings' => [

    ],

    'forms' => [
        'legends' => [
            'backup' => 'Архивация',
            'restore' => 'Восстановление',

        ],

        'attributes' => [
            'backup_complex' => 'Создать комплексный архив',
            'backup_database' => 'Дамп базы данных',
            'backup_system' => 'Системные файлы',
            'backup_theme' => 'Активная тема',
            'backup_uploads' => 'Вложенные файлы',

            'restore_complex' => 'Восстановление из комплексного архива',
            'restore_database' => 'Дамп базы данных',
            'restore_system' => 'Системные файлы',
            'restore_theme' => 'Активная тема',
            'restore_uploads' => 'Вложенные файлы',

        ],

        'labels' => [

        ],

        'placeholders' => [

        ],

        'validation' => [

        ],
    ],

    'messages' => [
        'errors' => [
            'isset_options' => 'Необходимо выбрать хотя бы одну опцию.',

        ],

    ],

];

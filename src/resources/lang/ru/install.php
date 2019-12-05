<?php

return [
    'headers' => [
        'welcome' => 'Приветствие',
        'permission' => 'Требования',
        'database' => 'База данных',
        'database-complete' => 'Миграции и наполнение БД',
        'common' => 'Общие параметры системы',

    ],

    'descriptions' => [
        'welcome' => '<p>Данный мастер поможет произвести установку, миграции и первоначальную настройку системы, например, активировать необходимые компоненты, выбрать тему оформления для сайта, а также создать учетную запись собственника сайта.</p><p>Установка состоит из нескольких шагов.</p>',
        'permission' => '<p>Для корректной работы системы, необходимо:</p><ul><li>сверить минимальные требования к серверу и наличие нижеуказанных расширений PHP;</li><li>убедиться в корректности отключения глобальных переменных;</li><li>проверить права доступа на запись к общим папкам и файлам.</li></ul></p><p>При наличии несоответствий уточните конфигурацию сервера у владельца хостинга, предоставившего услугу.</p>',
        'database' => '<p>Укажите параметры подключения к предварительно созданной базе данных. Конфигурацию сервера уточняйте у владельца хостинга, предоставившего услугу.</p><p hide>В пределах одной выбранной БД <mark>Префикс имени таблиц</mark> должен быть уникальным, а также на текущий момент <mark>2018-05</mark> может быть установлена только одна копия системы (проблема, связана с формированием <code>foreign_key</code>). В противном случае, будет выброшена ошибка.</p><p class="alert alert-warning">После нажатия на кнопку <kbd>Далее &raquo;</kbd> будут выполнены изменения в БД. Внимательнее при вводе данных!</p>',
        'database-complete' => '<p>Миграции успешно выполнены. Никаких дополнительных действий на этой странице не требуется.</p>',
        'common' => '<p>Для завершения установки необходимо указать название сайта и набор данных, которые будут записаны в файл переменных окружения <code>.env</code></p>',

    ],

    'strings' => [
        'requirements' => 'Требования скрипта',
        'globals' => 'Глобальные переменные',
        'files' => 'Доступы к файлам и папкам',
        'php' => 'PHP >= 7.2.0',
        'bcmath' => 'BCMath',
        'ctype' => 'Ctype',
        'curl' => 'cURL',
        'fileinfo' => 'Fileinfo',
        'gd' => 'GD',
        'json' => 'JSON',
        'mbstring' => 'Mbstring',
        'openssl' => 'OpenSSL',
        'pdo' => 'PDO mySql/MariaDB',
        'tokenizer' => 'Tokenizer',
        'xml' => 'XML',
        'zip' => 'ZIP',
        'zlib' => 'ZLib',

    ],

    'forms' => [
        'legends' => [
            'organization' => 'Официальные данные об организации',
            'owner' => 'Собственник сайта',
            'theme' => 'Тема оформления',

        ],

        'attributes' => [
            'APP_NAME' => 'Название сайта',
            'APP_THEME' => 'Шаблон',
            'DB_ENGINE' => 'Подсистема хранения (движок)',
            'DB_HOST' => 'Сервер БД',
            'DB_DATABASE' => 'Имя БД',
            'DB_PREFIX' => 'Префикс имени таблиц',
            'DB_USERNAME' => 'Имя пользователя',
            'DB_PASSWORD' => 'Пароль пользователя',
            'ORG_NAME' => 'Название организации',
            'ORG_ADDRESS_LOCALITY' => 'Населенный пункт',
            'ORG_ADDRESS_STREET' => 'Адрес',
            'ORG_CONTACT_TELEPHONE' => 'Контактный телефон',
            'ORG_CONTACT_EMAIL' => 'Контактный email',

            'licence' => 'Условия лицензионного соглашения',
            'test_seed' => 'Заполнение тестовыми данными',

        ],

        'labels' => [
            'APP_NAME' => 'Название сайта',
            'APP_THEME' => 'Шаблон',
            'DB_ENGINE' => 'Подсистема хранения (движок)',
            'DB_HOST' => 'Сервер, на котором находится БД <small class="form-text text-muted">Обычно <code>127.0.0.1</code>, либо <code>localhost</code></small>',
            'DB_DATABASE' => 'Имя предварительно созданной БД',
            'DB_PREFIX' => 'Префикс имени таблиц <small class="form-text text-muted">Будет добавлен ко всем создаваемым таблицам</small>',
            'DB_USERNAME' => 'Имя пользователя БД <small class="form-text text-muted">Обычно <code>root</code>, либо совпадает с именем БД</small>',
            'DB_PASSWORD' => 'Пароль пользователя БД',
            'ORG_NAME' => 'Название организации',
            'ORG_ADDRESS_LOCALITY' => 'Населенный пункт <small class="form-text text-muted">Страна, область/край, город</small>',
            'ORG_ADDRESS_STREET' => 'Адрес <small class="form-text text-muted">Улица, дом/строение</small>',
            'ORG_CONTACT_TELEPHONE' => 'Контактный телефон',
            'ORG_CONTACT_EMAIL' => 'Контактный email',

            'licence' => 'Я принимаю условия <a href="https://opensource.org/licenses/MIT" target="_blank">лицензионного соглашения</a>',
            'test_seed' => 'Заполнить тестовыми данными',

        ],

        'placeholders' => [
            'APP_NAME' => 'Мой блог',

        ],

        'validation' => [
            'some' => 'var',

        ],
    ],

    'messages' => [
        'denied_database_complete' => 'Прямой доступ на данную страницу запрещен.',
        'finish' => 'Установка успешно заверешена!',

    ],
];

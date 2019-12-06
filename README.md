## Ассистент приложения на Laravel 6.x
Ассистент приложения на Laravel 6.x является графической оболочкой для часто используемых команд консоли командной строки и включает в себя несколько пошаговых мастеров:
 - Установщик;
 - Мастер обновлений;
 - Архивариус;
 - Чистильщик.

### Подключение

 - **1** Для добавления зависимости в проект на Laravel в файле `composer.json`

    ```json
    "require": {
        "russsiq/laravel-assistant": "dev-master"
    }
    ```

 - **2** Для подключения в уже созданный проект воспользуйтесь командной строкой:

    ```console
    composer require russsiq/laravel-assistant:dev-master
    ```

 - **3** Если в вашем приложении включен отказ от обнаружения пакетов в директиве `dont-discover` в разделе `extra` файла `composer.json`, то необходимо самостоятельно добавить в файле `config/app.php`:

    - **3.1** Провайдер услуг в раздел `providers`:

        ```php
        Russsiq\Assistant\AssistantServiceProvider::class,
        ```

    - **3.2** Псевдоним класса (Facade) в раздел `aliases`:

        ```php
        'Assistant' => Russsiq\Assistant\Support\Facades\Assistant::class,
        ```

### Публикация файлов пакета

Публикация (копирование) всех доступных файлов для переопределения и тонкой настройки пакета осуществляется через интерфейс командной строки Artisan:

```console
php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider"
```

Помимо этого, доступна групповая публикация файлов по отдельным меткам `config`, `lang`, `views`:

```console
php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=config --force
```

```console
php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=lang --force
```

```console
php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider" --tag=views --force
```

### Краткое описание мастеров

![Assistant](screenshot.png)

#### Установщик
Состоит из следующих шагов:
 - **Приветствие** - экран с краткой вступительной речью и обязательным принятием лицензионного соглашения.
 - **Требования** - проверка на соответствие некоторых настроек сервера минимальным требованиям приложения.
 - **База данных** - необходимо указать параметры подключения к предварительно созданной БД.

    При нажатии кнопки <kbd>Далее</kbd> выполняется проверка подключения к БД, применяются миграции, расположенные в директории **database/migrations** вашего проекта.

    Помимо этого возможно наполнение БД начальными и фиктивными данными. Наполнители располагайте в директории **database/seeds** вашего проекта. Имена классов указывайте в опубликованном файле конфигурации `config/assistant.php` в разделе `installer.seeds`, где значениями для ключей является имена классов:
     - `database` - имя класса с начальными данными, например `'DatabaseSeeder'`;
     - `test` - имя класса с тестовыми данными, например `'TestContentSeeder'`.

     > В качестве имени класса принимается только один класс, записанный строкой. Не указывайте массивы!

 - **Миграции и наполнение БД** - информационный экран, отображающий результаты выполнения предыдущего шага.
 - **Общие параметры системы** - завершающий экран установки. Необходимо указать название сайта и набор данных, которые будут записаны в файл переменных окружения `.env`.



### Использование

#### Методы

Все публичные методы менеджера доступны через фасад `Assistant`:

```php
Assistant::someMethod(example $someParam);
```

Список доступных публичных методов:

 - [method](#method-method)

<a name="method-method"></a>
##### `method(): hint`
Описание метода.

#### Пример использования

```php
use Assistant;

// ... code
```

### Удаление пакета из вашего проекта на Laravel

```console
composer remove russsiq/laravel-assistant
```

### Тестирование

Неа, не слышал.

### Лицензия

`laravel-assistant` - программное обеспечение с открытым исходным кодом, распространяющееся по лицензии [MIT](https://choosealicense.com/licenses/mit/).

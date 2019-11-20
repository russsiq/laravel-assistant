## Ассистент приложения на Laravel 6.x.
 Ассистент приложения на Laravel 6.x.

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

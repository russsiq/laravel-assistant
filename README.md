# Ассистент приложения на Laravel 9.x

**Не используйте этот пакет – он не имеет тестов и не завершен**

## Введение

Ассистент приложения на Laravel 9.x является графической оболочкой для часто используемых команд консоли командной строки и включает в себя несколько пошаговых мастеров:

- [Установщик](#master-install);
- [Мастер обновлений](#master-update);
- [Архивариус](#master-archive);
- [Чистильщик](#master-clean).

При создании данного пакета преследовалась цель вынести часто повторяющиеся операции по обслуживанию и разворачивании небольших проектов на Laravel.

## Подключение

Для добавления зависимости в проект на Laravel, используйте менеджер пакетов Composer:

```console
composer require russsiq/laravel-assistant
```

Если в вашем приложении включен отказ от обнаружения пакетов в директиве `dont-discover` в разделе `extra` файла `composer.json`, то необходимо самостоятельно добавить следующее в файле `config/app.php`:

- Провайдер услуг в раздел `providers`:

```php
'providers' => [
    /*
     * Package Service Providers...
     */
    Russsiq\Assistant\AssistantServiceProvider::class,
],
```

- Псевдонимы классов (Фасады) в раздел `aliases`:

```php
'aliases' => [
    'Archivist' => Russsiq\Assistant\Facades\Archivist::class,
    'Cleaner' => Russsiq\Assistant\Facades\Cleaner::class,
    'Installer' => Russsiq\Assistant\Facades\Installer::class,
    'Updater' => Russsiq\Assistant\Facades\Updater::class,
],
```

### Публикация файлов пакета

Публикация (копирование) всех доступных файлов для переопределения и тонкой настройки пакета осуществляется через интерфейс командной строки Artisan:

```console
php artisan vendor:publish --provider="Russsiq\Assistant\AssistantServiceProvider"
```

Помимо этого, доступна групповая публикация файлов по отдельным меткам `config`, `lang`, `views` с префиксом `assistant-`:

```console
php artisan vendor:publish --tag=assistant-config --force
```

```console
php artisan vendor:publish --tag=assistant-lang --force
```

```console
php artisan vendor:publish --tag=assistant-views --force
```

> Флаг `--force` является необязательным и используется для принудительной перезаписи опубликованных раннее файлов пакета. Может быть полезен после обновления зависимостей.

### Ограничение прав доступа к разделам Ассистента

Следующие мастера имеют посредника `can:use-assistant` по всем маршрутам следующих мастеров:

 - Мастер обновлений;
 - Архивариус;
 - Чистильщик.

В поставщике вашего приложения `App\Providers\AuthServiceProvider` необходимо самостоятельно описать это правило доступа. Например, используя следующую конструкцию, измените значение `'example@email.com'` на ваше собственное:

```php
/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    //

    // Определить посредника, проверяющего,
    // что текущий пользователь имеет право
    // воспользоваться Ассистентом приложения.
    Gate::define('use-assistant', function ($user) {
        return in_array($user->email, [
            'example@email.com',
        ]);
    });
}
```

## Краткое описание мастеров

На изображении ниже представлен общий вид Ассистента приложения. Каждый из мастеров состоит из нескольких шагов. Каждый из шагов может быть представлен несколькими экранами: например, экран с вводом данных и экран, отображающий результат.

![Общий вид Ассистента](laravel-assistant.png)

Каждый из экранов после публикации файлов может быть переопределен в зависимости от ваших предпочтений и требований. Чаще всего требуется простое переопределение строк перевода в языковых файлах.

<a name="master-install"></a>
### Установщик

Приложение считается установленным только после того как в файле `.env` данным мастером будет прописана дата установки `APP_INSTALLED_AT`. До этого момента Установщик будет принудительно перенаправлять пользователя на маршрут Установщика.

Данный мастер состоит из нескольких экранов.

#### Экран Приветствие Установщика

Экран с краткой вступительной речью, запрашивающий у пользователя следубщие данные:

- названия сайта;
- текущее окружение;
- и принятие лицензионного соглашения.

До того как начнет выполняться указанный вами класс, Установщик дополнит запрос из формы полями `APP_DEBUG`, `APP_URL` (при их отсутствии) и выполнит валидацию следующих обязательных полей:

```php
// Режим отладки приложения.
'APP_DEBUG' => [
    'required',
    'boolean',
],

// Текущее окружение.
'APP_ENV' => [
    'required',
    Rule::in([
        'local',
        'dev',
        'testing',
        'production',
    ]),
],

// Название сайта.
'APP_NAME' => [
    'required',
    'string',
],

// Ссылка на главную страницу сайта.
'APP_URL' => [
    'required',
    'url',
],

// Принятие лицензионного соглашения.
'licence' => 'accepted',
```

#### Экран Требования Установщика

Проверка на соответствие некоторых настроек сервера минимальным требованиям приложения. Убедитесь, что все пункты будут отмечены зелеными галочками.

#### Экран База данных

Необходимо указать параметры подключения к предварительно созданной БД.

При нажатии кнопки <kbd>Далее</kbd> выполняется проверка подключения к БД, применяются миграции, расположенные в директории **database/migrations** вашего проекта.

Помимо этого возможно наполнение БД как начальными так и фиктивными данными. Наполнители располагайте в директории **database/seeds** вашего проекта. Имена классов указывайте в опубликованном файле конфигурации `config/assistant.php` в разделе `installer.seeds`, где значениями для ключей являются имена классов:

- `database` - имя класса с начальными данными, например `'DatabaseSeeder'`;
- `test` - имя класса с тестовыми данными, например `'TestContentSeeder'`.

> В качестве имени класса принимается только один класс, записанный строкой. Не указывайте массивы!

#### Экран Миграции и наполнение БД

Информационный экран, отображающий результаты выполнения предыдущего шага.

#### Экран Общие параметры системы

Завершающий экран установки. Необходимо указать набор данных, которые будут записаны в файл переменных окружения `.env`.

За вывод данного экрана отвечает шаблон `common.blade.php`, который будет доступен после публикации файлов пакета в директории `resources\views\vendor\assistant\install`.

Поля ввода, заданные вами в этой форме разделяются на два типа: предназначенные для записи в файл `.env` и не предназначенные для этого.

Для записи переменных окружения и их значений в файл переменных окружения `.env`, имена полей ввода должны быть в верхнем регистре и в качестве разделителя использовать нижнее подчеркивание, например:

```html
<input type="text" name="SOME_VAR" value="{{ old('SOME_VAR', 'default') }}" />

<select name="OTHER_VAR">
    <!-- остальная разметка -->
</select>
```

Поля ввода, имена которых не соответствуют этому правилу **не будут записаны** в файл `.env` и могут использоваться вами для построения бизнес-логики в классе финальной стадии Установщика.

Данный класс необходимо предварительно сгенерировать с помощью команды:

```console
php artisan make:before_installed BeforeInstalled --force
```

Эта команда создаст файл `app\Services\Assistant\BeforeInstalled.php`. Укажите Ассистенту, что он должен использовать этот сгенерированный класс финальной стадии Установщика в опубликованном файле конфигурации `config/assistant.php` в разделе `installer`:

```
'before-installed' => App\Services\Assistant\BeforeInstalled::class,
```

> В данном классе вы **обязаны самостоятельно выполнить валидацию** добавленных вами полей в форме `common.blade.php`.

В данном классе вы можете добавлять поля либо изменять значения полей, предназначенных для записи в файл переменных окружения `.env`. Например, добавим абстрактное поле:

```php
// Меняем название темы сайта для
// последующей записи в файл окружения.
$request->merge([
    'APP_THEME' => $theme,
]);
```

В вашем распоряжении также имеется возможность в файле настроек `config/assistant.php` дополнительно указать директории для копирования и создания ссылок:

```php
// Копирование директорий: fromDir, toDir.
'directories' => [
    // 'fromDir' => 'toDir',
],

// Создание ссылок на директории: target => link.
'symlinks' => [
    storage_path('app/public') => public_path('storage'),
],
```

<a name="master-update"></a>
### Мастер обновлений

Простой мастер, состоящий из двух экранов:

 - Экран выбора опций обновления.
 - Экран, отображающий результаты выполнения предыдущего шага.

<a name="master-archive"></a>
### Архивариус

Простой мастер, состоящий из двух экранов:

 - Экран выбора опций архивации.
 - Экран, отображающий результаты выполнения предыдущего шага.

<a name="master-clean"></a>
### Чистильщик

Простой мастер, состоящий из двух экранов:

 - Экран выбора опций очистки или кеширования.
 - Экран, отображающий результаты выполнения предыдущего шага.

## Удаление пакета из вашего проекта на Laravel

```console
composer remove russsiq/laravel-assistant
```

## Тестирование

Неа, не слышал.

## Лицензия

`laravel-assistant` - программное обеспечение с открытым исходным кодом, распространяющееся по лицензии [MIT](https://choosealicense.com/licenses/mit/).

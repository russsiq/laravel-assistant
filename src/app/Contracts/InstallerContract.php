<?php

namespace Russsiq\Assistant\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

interface InstallerContract
{
    /**
     * Инициировать начальный этап установки.
     * @return void
     */
    public function initiate();

    /**
     * Маркер того, что была выполнена
     * первоначальная инициализация установки.
     * @return boolean
     */
    public function alreadyInitiated(): bool;

    /**
     * Маркер, что приложение установлено.
     * @return boolean
     */
    public function alreadyInstalled(): bool;

    /**
     * Получить дату установки приложения.
     * @return mixed
     */
    public function installedAt();

    /**
     * Получить массив с набором минимальных системных требований к серверу.
     * @return array
     */
    public static function requirements(): array;

    /**
     * Получить массив "зловредных" глобальных переменных.
     * @return array
     */
    public static function antiGlobals(): array;

    /**
     * Получить массив прав на доступ к директориям.
     * @return array
     */
    public static function filePermissions(): array;

    /**
     * Получить массив доступных при установке тем.
     * @return array
     */
    public static function themes(): array;

    /**
     * Выполнить проверку подключения к БД с переданными параметрами.
     * @return void
     *
     * @throws InstallerFailed
     */
    public function checkConnection(array $params, string $connection);

    /**
     * Выполнить миграции БД.
     * @return string  Сообщение о выполненной операции.
     */
    public function migrate(): string;

    /**
     * Заполнить БД данными.
     * @param  string  $class Класс заполнителя.
     * @return string  Сообщение о выполненной операции.
     */
    public function seed(string $class): string;

    /**
     * Посредник, выполняющий заданные операции
     * на завершающей стадии установки приложения.
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function beforeInstalled(Request $request): RedirectResponse;

    /**
     * Копирование директорий, заданных в массиве конфигурации.
     * @return void
     */
    public function copyDirectories();

    /**
     * Копирование директории со всеми файлами.
     * @param  string $fromDir
     * @param  string $toDir
     * @return void
     */
    public function copyDirectory(string $fromDir, string $toDir);

    /**
     * Создание ссылок, заданных в массиве конфигурации.
     * @return void
     */
    public function createSymbolicLinks();

    /**
     * Создание ссылки.
     * @param  string $target
     * @param  string $link
     * @return void
     */
    public function createSymbolicLink(string $target, string $link);

    /**
     * Применить замыкание, если переданное условие `$condition` правдиво.
     * @param  bool  $condition
     * @param  callable  $callback
     * @return self
     */
    public function when(bool $value, callable $callback): self;
}

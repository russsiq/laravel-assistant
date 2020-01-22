<?php

namespace Russsiq\Assistant\Support\Contracts;

interface InstallerContract
{
    /**
     * Инициировать начальный этап установки.
     *
     * @return void
     */
    public function initiate();

    /**
     * Маркер того, что была выполнена
     * первоначальная инициализация установки.
     *
     * @return boolean
     */
    public function alreadyInitiated(): bool;

    /**
     * Маркер, что приложение установлено.
     *
     * @return boolean
     */
    public function alreadyInstalled(): bool;

    /**
     * Получить массив с набором минимальных системных требований к серверу.
     *
     * @return array
     */
    public static function requirements(): array;

    /**
     * Получить массив "зловредных" глобальных переменных.
     *
     * @return array
     */
    public static function antiGlobals(): array;

    /**
     * Получить массив прав на доступ к директориям.
     *
     * @return array
     */
    public static function filePermissions(): array;

    /**
     * Получить массив доступных при установке тем.
     *
     * @return array
     */
    public static function themes(): array;

    /**
     * Выполнить проверку подключения к БД с переданными параметрами.
     *
     * @return void
     */
    public function checkConnection(array $params, string $connection);

    /**
     * Выполнить миграции БД.
     *
     * @return string   Сообщение о выполненной операции.
     */
    public function migrate(): string;

    /**
     * Заполнить БД данными.
     *
     * @param  string $class Класс заполнителя.
     *
     * @return string        Сообщение о выполненной операции.
     */
    public function seed(string $class): string;

    /**
     * Применить замыкание, если переданное значение `$value` правдиво.
     *
     * @param  bool  $value
     * @param  callable  $callback
     *
     * @return self
     */
    public function when(bool $value, callable $callback): self;
}

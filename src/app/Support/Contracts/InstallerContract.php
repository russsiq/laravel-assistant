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
     * Получить массив с минимальными требованиями.
     *
     * @return array
     */
    public function requirements(): array;

    /**
     * Получить массив "зловредных" переменных.
     *
     * @return array
     */
    public function antiGlobals(): array;

    /**
     * Получить массив прав на доступ к директориям.
     *
     * @return array
     */
    public function filePermissions(): array;

    /**
     * Получить массив доступных при установке тем.
     *
     * @return array
     */
    public function themes(): array;
}

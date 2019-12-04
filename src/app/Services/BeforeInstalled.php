<?php

namespace Russsiq\Assistant\Services;

use Russsiq\Assistant\Services\Abstracts\AbstractBeforeInstalled;

/**
 * Класс, который выполняется на финальной стадии,
 * перед тем как приложение будет отмечено как "установленное".
 *
 * Позволяет пользователю пакета определить свою логику валидации данных,
 * которые будут внесены в файл переменных окружения,
 * а также логику регистрации собственника сайта.
 */
class BeforeInstalled extends AbstractBeforeInstalled
{
    //
}

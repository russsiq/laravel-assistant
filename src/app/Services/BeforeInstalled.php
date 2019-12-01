<?php

namespace Russsiq\Assistant\Services;

use Russsiq\Assistant\Services\Abstracts\AbstractBeforeInstalled;

/**
 * Класс, который выполняется на финальной стадии.
 * Перед тем как приложение будет отмечено как "установленное".
 * Позволяет пользователю пакета определить свою логику.
 */
class BeforeInstalled extends AbstractBeforeInstalled
{
    //
}

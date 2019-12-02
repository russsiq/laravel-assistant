<?php

namespace Russsiq\Assistant\Services\Abstracts;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;

use Russsiq\Assistant\Services\Contracts\BeforeInstalledContract;

abstract class AbstractBeforeInstalled implements BeforeInstalledContract
{
    /**
     * Получить валидатор для проверки входящих данных запроса.
     *
     * @param  array  $data
     *
     * @return ValidatorContract
     */
    abstract protected function validator(array $data): ValidatorContract;
}

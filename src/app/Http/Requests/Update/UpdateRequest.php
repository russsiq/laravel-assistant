<?php

namespace Russsiq\Assistant\Http\Requests\Update;

use Russsiq\Assistant\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Подготовить данные для валидации.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $input = $this->except([
            '_token',
            '_method',
            'submit',
        ]);

        $this->replace($input);
    }
}

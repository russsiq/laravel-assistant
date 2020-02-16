<?php

namespace Russsiq\Assistant\Http\Requests\Install;

use Russsiq\Assistant\Http\Requests\Request;

class PermissionRequest extends Request
{
    /**
     * Получить массив правил валидации,
     * которые будут применены к запросу.
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * Получить массив пользовательских строк
     * для формирования сообщений валидатора.
     * @return array
     */
    public function messages()
    {
        return [

        ];
    }

    /**
     * Получить пользовательские имена атрибутов
     * для формирования сообщений валидатора.
     * @return array
     */
    public function attributes()
    {
        return [

        ];
    }
}

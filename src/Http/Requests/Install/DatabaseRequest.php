<?php

namespace Russsiq\Assistant\Http\Requests\Install;

use Russsiq\Assistant\Http\Requests\Request;

class DatabaseRequest extends Request
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

        $this->replace($input)
            ->merge([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $this->input('DB_HOST', '127.0.0.1'),
                'DB_PORT' => $this->input('DB_PORT', '3306'),
                'test_seed' => $this->input('test_seed', false),
            ]);
    }

    /**
     * Получить массив правил валидации,
     * которые будут применены к запросу.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'DB_CONNECTION' => [
                'bail',
                'required',
                'string',
                'in:mysql',
            ],

            'DB_HOST' => [
                'bail',
                'required',
                'string',
            ],

            'DB_PORT' => [
                'bail',
                'required',
                'integer',
            ],

            'DB_DATABASE' => [
                'bail',
                'required',
                'string',
            ],

            'DB_PREFIX' => [
                'bail',
                'required',
                'string',
            ],

            'DB_USERNAME' => [
                'bail',
                'required',
                'string',
            ],

            'DB_PASSWORD' => [
                'bail',
                'nullable',
                'string',
            ],

            'test_seed' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Получить массив пользовательских строк
     * для формирования сообщений валидатора.
     *
     * @return array
     */
    public function messages()
    {
        $trans = trans('assistant::install.forms.validation');

        return is_array($trans) ? $trans : [];
    }

    /**
     * Получить пользовательские имена атрибутов
     * для формирования сообщений валидатора.
     *
     * @return array
     */
    public function attributes()
    {
        $trans = trans('assistant::install.forms.attributes');

        return is_array($trans) ? $trans : [];
    }
}

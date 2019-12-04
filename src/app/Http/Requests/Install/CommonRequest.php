<?php

namespace Russsiq\Assistant\Http\Requests\Install;

use Russsiq\Assistant\Http\Requests\Request;

class CommonRequest extends Request
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $input = $this->except([
            '_token',
            '_method',
            'submit',
            'APP_INSTALLED_AT',

        ]);

        return $this->replace($input)
            ->merge([
                // Режим отладки приложения.
                'APP_DEBUG' => $input['APP_DEBUG'] ?? false,

                // Ссылка на главную страницу сайта.
                'APP_URL' => $input['APP_URL'] ?? url('/'),

            ])
            ->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Режим отладки приложения.
            'APP_DEBUG' => [
                'required',
                'boolean',

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
        ];
    }
}

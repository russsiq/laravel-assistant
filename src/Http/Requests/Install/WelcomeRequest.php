<?php

namespace Russsiq\Assistant\Http\Requests\Install;

use Illuminate\Validation\Rule;
use Russsiq\Assistant\Http\Requests\Request;

class WelcomeRequest extends Request
{
    /**
     * Подготовить данные для валидации.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $input = $this->only([
            'APP_DEBUG',
            'APP_ENV',
            'APP_NAME',
            'APP_URL',
            'licence',
        ]);

        $this->replace($input)
            ->merge([
                // Режим отладки приложения.
                'APP_DEBUG' => $input['APP_DEBUG'] ?? false,

                // Текущее окружение.
                'APP_ENV' => $input['APP_ENV'] ?? 'local',

                // Ссылка на главную страницу сайта.
                'APP_URL' => $input['APP_URL'] ?? url('/'),
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
            // Режим отладки приложения.
            'APP_DEBUG' => [
                'required',
                'boolean',
            ],

            // Текущее окружение.
            'APP_ENV' => [
                'required',
                Rule::in([
                    'local',
                    'dev',
                    'testing',
                    'production',
                ]),
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

            // Принятие лицензионного соглашения.
            'licence' => 'accepted',
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

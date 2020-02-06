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

    /**
     * Получить правила валидации,
     * которые будут применены к запросу.
     *
     * @return array
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * Получить пользовательские строки
     * для формирования сообщений валидатора.
     *
     * @return array
     */
    public function messages()
    {
        $trans = trans('assistant::update.forms.validation');

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
        $trans = trans('assistant::update.forms.attributes');

        return is_array($trans) ? $trans : [];
    }
}

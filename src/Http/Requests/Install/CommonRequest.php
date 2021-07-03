<?php

namespace Russsiq\Assistant\Http\Requests\Install;

use Russsiq\Assistant\Http\Requests\Request;

class CommonRequest extends Request
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
            'APP_INSTALLED_AT',
        ]);

        $this->replace($input)
            ->merge([
                //
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
            //
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

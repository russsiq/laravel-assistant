<?php

namespace Russsiq\Assistant\Http\Requests\Clean;

use Illuminate\Validation\Validator;

use Russsiq\Assistant\Http\Requests\Request;

class CleanRequest extends Request
{
    /**
     * Подготовить данные для валидации.
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

            ]);
    }

    /**
     * Получить массив правил валидации,
     * которые будут применены к запросу.
     * @return array
     */
    public function rules()
    {
        return [
            'clear_cache' => 'boolean',
            'clear_config' => 'boolean',
            'clear_route' => 'boolean',
            'clear_view' => 'boolean',
            'clear_compiled' => 'boolean',

            'cache_config' => 'boolean',
            'cache_route' => 'boolean',
            'cache_view' => 'boolean',

            'complex_optimize' => 'boolean',

        ];
    }

    /**
     * Получить массив пользовательских строк
     * для формирования сообщений валидатора.
     * @return array
     */
    public function messages()
    {
        $trans = trans('assistant::clean.forms.validation');

        return is_array($trans) ? $trans : [];
    }

    /**
     * Получить пользовательские имена атрибутов
     * для формирования сообщений валидатора.
     * @return array
     */
    public function attributes()
    {
        $trans = trans('assistant::clean.forms.attributes');

        return is_array($trans) ? $trans : [];
    }

    /**
     * Надстройка экземпляра валидатора.
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {
            if (empty($this->keys())) {
                $validator->errors()->add(
                    'isset_options',
                    trans('assistant::clean.messages.errors.isset_options')
                );
            }
        });
    }
}

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

        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $trans = trans('assistant::install.forms.validation');

        return is_array($trans) ? $trans : [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        $trans = trans('assistant::install.forms.attributes');

        return is_array($trans) ? $trans : [];
    }
}

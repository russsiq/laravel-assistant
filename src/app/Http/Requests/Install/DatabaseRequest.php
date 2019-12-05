<?php

namespace Russsiq\Assistant\Http\Requests\Install;

use Russsiq\Assistant\Http\Requests\Request;

class DatabaseRequest extends Request
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
        ]);

        return $this->replace($input)
            ->merge([
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $this->input('DB_HOST', '127.0.0.1'),
                'DB_PORT' => $this->input('DB_PORT', '3306'),
                'test_seed' => $this->input('test_seed', false),
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

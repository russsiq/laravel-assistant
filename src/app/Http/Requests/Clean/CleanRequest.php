<?php

namespace Russsiq\Assistant\Http\Requests\Clean;

use Russsiq\Assistant\Http\Requests\Request;

class CleanRequest extends Request
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
            'clear_cache' => 'boolean',
            'clear_view' => 'boolean',

            // 'cache' => 'array',
            // 'cache.*' => 'required|boolean',
            //
            // 'optimize' => 'array',
            // 'optimize.*' => 'required|boolean',

        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $trans = trans('assistant::clean.forms.validation');

        return is_array($trans) ? $trans : [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * NOTE: Не оборачивать в коллекции - это только усложнит код.
     *
     * @return array
     */
    public function attributes()
    {
        $trans = trans('assistant::clean.forms.attributes');

        return is_array($trans) ? $trans : [];

        $output = [];

        foreach ($trans as $prefix => $child) {
            foreach ($child as $key => $value) {
                $output[$prefix.'.'.$key] = $value;
            }
        }

        return $output;
    }
}

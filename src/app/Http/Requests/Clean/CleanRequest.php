<?php

namespace Russsiq\Assistant\Http\Requests\Clean;

use Russsiq\Assistant\Http\Requests\Request;

class CleanRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clean' => 'array',
            'clean.*' => 'boolean',

            'cache' => 'array',
            'cache.*' => 'boolean',
            
            'optimize' => 'array',
            'optimize.*' => 'boolean',

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
     * @return array
     */
    public function attributes()
    {
        $trans = trans('assistant::clean.forms.attributes');

        return is_array($trans) ? $trans : [];
    }
}

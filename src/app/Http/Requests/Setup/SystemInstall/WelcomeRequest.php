<?php

namespace Russsiq\Assistant\Http\Requests\Setup\SystemInstall;

use Russsiq\Assistant\Http\Requests\Request;

class WelcomeRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'agree' => [
                'required',
                'boolean'
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
        return [
            'agree.*' => __('msg.not_accept_licence'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }
}
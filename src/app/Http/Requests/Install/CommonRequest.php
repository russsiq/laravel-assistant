<?php

namespace Russsiq\Assistant\Http\Requests\Install;

use Russsiq\Assistant\Http\Requests\Request;
use Russsiq\Assistant\Exceptions\InstallerFailed;

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
        ]);

        if (empty($input['original_theme'])) {
            $input['APP_THEME'] = Str::slug($this->input('APP_NAME', 'default'));
        }

        return $this->replace($input)
            ->merge([
                //
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
            'APP_NAME' => [
                'required',
                'string',
            ],

            'APP_THEME' => [
                'required',
                'string',
                'in:'.implode(',', select_dir('themes')),
            ],

            'name' => [
                'required',
                'string',
                'between:3,255',
            ],

            'email' => [
                'required',
                'string',
                'between:6,255',
                'email',
                'unique:users',
            ],

            'password' => [
                'required',
                'string',
                'between:6,255',
            ],

            'original_theme' => [
                'sometimes',
                'boolean',
            ],

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
            'APP_NAME' => __('APP_NAME'),
            'APP_THEME' => __('APP_THEME'),
            'name' => __('common.name'),
            'email' => __('common.email'),
            'password' => __('common.password'),

        ];
    }
}

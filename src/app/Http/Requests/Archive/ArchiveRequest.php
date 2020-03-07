<?php

namespace Russsiq\Assistant\Http\Requests\Archive;

// Сторонние зависимости.
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Russsiq\Assistant\Http\Requests\Request;

class ArchiveRequest extends Request
{
    /**
     * Общий массив допустимых значений для правила `in:список_значений`.
     * @var array
     */
    protected $allowedForInRule = [
        'backup' => [
            'complex',
            'database',
            'system',
            'theme',
            'uploads',

        ],

        'restore' => [
            'complex',
            'database',
            'system',
            'theme',
            'uploads',

        ],

    ];

    /**
     * Подготовить данные для валидации.
     * @return void
     */
    protected function prepareForValidation(): void
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
     * Получить пользовательские имена атрибутов
     * для формирования сообщений валидатора.
     * @return array
     */
    public function attributes(): array
    {
        $trans = trans('assistant::archive.forms.attributes');

        return is_array($trans) ? $trans : [];
    }

    /**
     * Получить массив пользовательских строк перевода
     * для формирования сообщений валидатора.
     * @return array
     */
    public function messages(): array
    {
        $trans = trans('assistant::archive.forms.validation');

        return is_array($trans) ? $trans : [];
    }

    /**
     * Получить массив правил валидации,
     * которые будут применены к запросу.
     * @return array
     */
    public function rules(): array
    {
        return [
            'backup' => [
                'sometimes',
                'string',
                'in:'.$this->allowedForInRule('backup'),

            ],

            'restore' => [
                'sometimes',
                'string',
                'in:'.$this->allowedForInRule('restore'),

            ],

        ];
    }

    /**
     * Надстройка экземпляра валидатора.
     * @param  ValidatorContract  $validator
     * @return void
     */
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            if (empty($this->keys())) {
                $validator->errors()->add(
                    'isset_options',
                    trans('assistant::archive.messages.errors.isset_options')
                );
            }
        });
    }
}

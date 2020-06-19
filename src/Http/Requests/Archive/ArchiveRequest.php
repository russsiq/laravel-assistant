<?php

namespace Russsiq\Assistant\Http\Requests\Archive;

// Зарегистрированные фасады приложения.
use Russsiq\Assistant\Facades\Archivist;

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
        Archivist::KEY_NAME_OPERATOR => [
            'backup',
            'restore',

        ],

        'options' => [
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

        if ('backup' === $input[Archivist::KEY_NAME_OPERATOR]) {
            unset($input['restore']);
            unset($input['filename']);
        }
        elseif ('restore' === $input['operator']) {
            unset($input['backup']);
        }

        // TODO: Предварительная валидация файла
        //       на его физическое присутствие
        //       и доступность для чтения.

        // if ($this->filled('filename')) {
        //     dd('filename');
        // }

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
                'array',

            ],

            'backup.*' => [
                'required',
                'string',
                'in:'.$this->allowedForInRule('options'),

            ],

            'restore' => [
                'array',
                'required_without:backup'

            ],

            'restore.*' => [
                'required',
                'string',
                'in:'.$this->allowedForInRule('options', [
                    'system',

                ]),

            ],

            'filename' => [
                'string',
                "required_if:{Archivist::KEY_NAME_OPERATOR},restore",
                // 'in:'

            ],

        ];
    }
}

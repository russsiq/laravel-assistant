<?php

namespace Russsiq\Assistant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * Общий массив допустимых значений для правила `in:список_значений`.
     * @var array
     */
    protected $allowedForInRule = [
        // 'key' => ['one', 'two', 'other'],
    ];

    /**
     * Получить список допустимых значений для правила `in:список_значений`.
     * @param  string  $key
     * @return string
     */
    protected function allowedForInRule(string $key): string
    {
        return implode(',', $this->allowedForInRule[$key]);
    }

    public function authorize()
    {
        return true;
    }
}

<?php

namespace App\Http\Requests\Watcher;

use Illuminate\Foundation\Http\FormRequest;

class CheckRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'url' => 'required|url',
            'xpath_value' => 'required|string|min:2',
            'xpath_title' => 'nullable|string',
        ];
    }
}

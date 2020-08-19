<?php

namespace App\Http\Requests\Watcher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:2',
            'query' => 'nullable|string|min:2',
            'url' => 'nullable|url',
            'xpath_name' => 'nullable|string|min:2',
            'interval_id' => 'nullable|exists:intervals,id',
            'alert_value' => 'nullable|numeric',
        ];
    }
}

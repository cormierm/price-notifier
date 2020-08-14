<?php

namespace App\Http\Requests\Watcher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'query' => 'nullable|string',
            'url' => 'nullable|string',
            'interval_id' => 'nullable|exists:intervals,id',
            'alert_value' => 'nullable|string',
        ];
    }
}

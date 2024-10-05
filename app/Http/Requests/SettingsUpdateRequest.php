<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pushover_user_key' => ['nullable', 'string', 'size:30'],
            'pushover_api_token' => ['nullable', 'string', 'size:30'],
            'user_agent' => ['nullable', 'string', 'max:255'],
        ];
    }
}

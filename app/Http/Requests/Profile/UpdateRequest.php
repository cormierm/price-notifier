<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'pushover_user_key' => 'nullable|string|size:30',
            'pushover_api_token' => 'nullable|string|size:30',
            'user_agent' => 'nullable|string|max:255',
            'api_key' => 'nullable|uuid',
            'phone_number' => 'nullable|regex:/^\+[0-9]{5,20}$/',
        ];
    }
}

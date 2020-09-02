<?php

namespace App\Http\Requests\Template;

use App\Utils\HtmlFetcher;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{

    public function rules()
    {
        return [
            'xpath_value' => 'nullable|string|min:2|max:191',
            'client' => 'nullable|in:' . implode(',', HtmlFetcher::CLIENTS),
        ];
    }
}

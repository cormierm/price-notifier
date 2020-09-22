<?php

namespace App\Http\Requests\Watcher;

use App\Utils\HtmlFetcher;
use Illuminate\Foundation\Http\FormRequest;

class CheckRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'url' => 'required|url',
            'xpath_value' => 'required|string|min:2',
            'client' => 'required|in:' . implode(',', HtmlFetcher::CLIENTS),
            'xpath_stock' => 'nullable|string|max:191',
            'stock_text' => 'nullable|string|max:191',
            'stock_contains' => 'nullable|boolean',
        ];
    }
}

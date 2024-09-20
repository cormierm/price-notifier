<?php

namespace App\Http\Requests\Template;

use App\Models\Watcher;
use App\Utils\Fetchers\HtmlFetcher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{

    public function rules()
    {
        return [
            'price_query' => 'nullable|string|min:2|max:255',
            'price_query_type' => ['nullable', Rule::in(Watcher::QUERY_TYPES)],
            'stock_query' => 'nullable|string|max:255',
            'stock_query_type' => ['nullable', Rule::in(Watcher::QUERY_TYPES)],
            'stock_text' => 'nullable|string|max:255',
            'stock_condition' => ['nullable', Rule::in(Watcher::STOCK_CONDITIONS)],
            'stock_requires_price' => 'nullable|boolean',
            'client' => 'nullable|in:' . implode(',', HtmlFetcher::CLIENTS),
        ];
    }
}

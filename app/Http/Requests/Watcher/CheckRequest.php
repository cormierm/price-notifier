<?php

namespace App\Http\Requests\Watcher;

use App\Utils\Fetchers\HtmlFetcher;
use App\Utils\HtmlParser;
use App\Watcher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'url' => 'required|url',
            'price_query_type' => ['nullable', Rule::in([HtmlParser::QUERY_TYPE_REGEX, HtmlParser::QUERY_TYPE_XPATH])],
            'price_query' => 'nullable|string|min:2',
            'client' => 'required|in:' . implode(',', HtmlFetcher::CLIENTS),
            'xpath_stock' => 'nullable|string|max:255',
            'stock_text' => 'nullable|string|max:255',
            'stock_condition' => ['nullable', Rule::in(Watcher::STOCK_CONDITIONS)],
        ];
    }
}

<?php

namespace App\Http\Requests\Watcher;

use App\Utils\Fetchers\HtmlFetcher;
use App\Watcher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:2|max:255',
            'price_query' => 'nullable|string|min:2|max:255',
            'price_query_type' => ['nullable', Rule::in(Watcher::QUERY_TYPES)],
            'stock_query' => 'nullable|string|max:255',
            'stock_query_type' => ['nullable', Rule::in(Watcher::QUERY_TYPES)],
            'url' => 'nullable|url|max:255',
            'xpath_name' => 'nullable|string|min:2|max:255',
            'interval_id' => 'nullable|exists:intervals,id',
            'region_id' => 'nullable|exists:regions,id',
            'alert_value' => 'nullable|numeric',
            'client' => 'nullable|in:' . implode(',', HtmlFetcher::CLIENTS),
            'stock_text' => 'nullable|string|max:255',
            'stock_alert' => 'nullable|boolean',
            'stock_requires_price' => 'nullable|boolean',
            'stock_condition' => ['nullable', Rule::in(Watcher::STOCK_CONDITIONS)],
            'update_queries' => 'nullable|boolean',
        ];
    }
}

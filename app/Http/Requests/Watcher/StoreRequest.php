<?php

namespace App\Http\Requests\Watcher;

use App\Models\Watcher;
use App\Utils\Fetchers\HtmlFetcher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'url' => 'required|url|max:255',
            'price_query'  => 'required|string|min:2|max:255',
            'price_query_type' => ['nullable', Rule::in(Watcher::QUERY_TYPES)],
            'stock_query'  => 'nullable|string|max:255',
            'stock_query_type' => ['nullable', Rule::in(Watcher::QUERY_TYPES)],
            'stock_condition' => ['nullable', Rule::in(Watcher::STOCK_CONDITIONS)],
            'stock_text' => 'nullable|string|max:255',
            'stock_alert' => 'nullable|boolean',
            'stock_requires_price' => 'nullable|boolean',
            'interval_id' => 'nullable|exists:intervals,id',
            'region_id' => 'nullable|exists:regions,id',
            'alert_value' => 'nullable|numeric',
            'alert_condition' => ['nullable', Rule::in(Watcher::ALERT_CONDITIONS)],
            'client' => 'required|in:' . implode(',', HtmlFetcher::CLIENTS),
            'update_queries' => 'nullable|boolean',
        ];
    }
}

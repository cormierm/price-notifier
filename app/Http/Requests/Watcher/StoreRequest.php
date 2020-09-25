<?php

namespace App\Http\Requests\Watcher;

use App\Utils\HtmlFetcher;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:191',
            'url' => 'required|url|max:191',
            'query'  => 'required|string|min:2|max:191',
            'interval_id' => 'nullable|exists:intervals,id',
            'region_id' => 'nullable|exists:regions,id',
            'alert_value' => 'nullable|numeric',
            'client' => 'required|in:' . implode(',', HtmlFetcher::CLIENTS),
            'xpath_stock' => 'nullable|string|max:191',
            'stock_text' => 'nullable|string|max:191',
            'stock_alert' => 'nullable|boolean',
            'stock_contains' => 'nullable|boolean',
            'update_queries' => 'nullable|boolean',
        ];
    }
}

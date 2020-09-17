<?php

namespace App\Http\Requests\Watcher;

use App\Utils\HtmlFetcher;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:2|max:191',
            'query' => 'nullable|string|min:2|max:191',
            'url' => 'nullable|url|max:191',
            'xpath_name' => 'nullable|string|min:2|max:191',
            'interval_id' => 'nullable|exists:intervals,id',
            'region_id' => 'nullable|exists:regions,id',
            'alert_value' => 'nullable|numeric',
            'client' => 'nullable|in:' . implode(',', HtmlFetcher::CLIENTS),
            'xpath_stock' => 'nullable|string|max:191',
            'stock_text' => 'nullable|string|max:191',
            'stock_alert' => 'nullable|boolean',
        ];
    }
}

<?php

namespace App\Http\Requests\Watcher;

use App\Utils\HtmlFetcher;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2',
            'url' => 'required|url',
            'query'  => 'required|string|min:2',
            'xpath_name' => 'required|string|min:2',
            'interval_id' => 'nullable|exists:intervals,id',
            'alert_value' => 'nullable|numeric',
            'client' => 'required|in:' . implode(',', HtmlFetcher::CLIENTS),
        ];
    }
}

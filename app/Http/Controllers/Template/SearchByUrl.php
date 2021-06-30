<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchByUrl extends Controller
{
    public function __invoke(Request $request)
    {
        $template = $request->user()
            ->templates()
            ->where('domain', str_replace('www.', '', parse_url($request->input('url'), PHP_URL_HOST)))
            ->firstOrFail();

        return new JsonResponse([
            'domain' => $template->domain,
            'price_query' => $template->price_query,
            'price_query_type' => $template->price_query_type,
            'stock_query' => $template->stock_query,
            'stock_query_type' => $template->stock_query_type,
            'client' => $template->client,
            'stock_text' => $template->stock_text,
            'stock_condition' => $template->stock_condition,
        ]);
    }
}

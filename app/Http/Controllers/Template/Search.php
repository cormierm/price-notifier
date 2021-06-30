<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Search extends Controller
{
    public function __invoke(Request $request, $domain)
    {
        $template = $request->user()
            ->templates()
            ->where('domain', $domain)
            ->firstOrFail();

        return new JsonResponse([
            'domain' => $template->domain,
            'xpath_name' => $template->xpath_name,
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

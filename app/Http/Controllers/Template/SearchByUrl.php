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
            'xpath_name' => $template->xpath_name,
            'xpath_value' => $template->xpath_value,
            'client' => $template->client,
        ]);
    }
}

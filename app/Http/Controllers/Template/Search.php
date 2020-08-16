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
            'xpath_value' => $template->xpath_value,
        ]);
    }
}

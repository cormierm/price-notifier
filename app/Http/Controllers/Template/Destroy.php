<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Destroy extends Controller
{
    public function __invoke(Request $request, Template $template): JsonResponse
    {
        $template->delete();

        return new JsonResponse([
            'message' => 'Successfully deleted template',
        ]);
    }
}

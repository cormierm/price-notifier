<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Http\Requests\Template\UpdateRequest;
use App\Template;
use Illuminate\Http\JsonResponse;

class Update extends Controller
{
    public function __invoke(UpdateRequest $request, Template $template): JsonResponse
    {
        $template->update($request->validated());

        return new JsonResponse([
            'message' => 'Successfully updated',
            'template' => $template
        ]);
    }
}

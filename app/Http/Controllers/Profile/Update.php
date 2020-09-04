<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateRequest;
use Illuminate\Http\JsonResponse;

class Update extends Controller
{
    public function __invoke(UpdateRequest $request)
    {
        $request->user()->update($request->validated());

        return new JsonResponse($request->validated());
    }
}

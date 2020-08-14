<?php

namespace App\Http\Controllers\Watcher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Watcher\UpdateRequest;
use App\Watcher;
use Illuminate\Http\JsonResponse;

class Update extends Controller
{
    public function __invoke(UpdateRequest $request, Watcher $watcher): JsonResponse
    {
        $watcher->update($request->validated());

        return new JsonResponse([
            'message' => 'Successfully updated',
            'watcher' => $watcher,
        ]);
    }
}

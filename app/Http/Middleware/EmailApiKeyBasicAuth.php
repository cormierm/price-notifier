<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Exception;

class EmailApiKeyBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userQuery = null;
        try {
            $basicAuthInfo = explode(":", base64_decode(explode(" ", $request->header('Authorization'))[1]));
            $userQuery = User::query()
                ->where('email', $basicAuthInfo[0])
                ->where('api_key', $basicAuthInfo[1]);
        } catch (Exception $e) {
            return response()->json('Unauthorized', 401);
        }

        if ($userQuery->exists()) {
            $request->setUserResolver(function () use ($userQuery) {
                return $userQuery->first();
            });

            return $next($request);
        }

        return response()->json('Unauthorized', 401);
    }
}

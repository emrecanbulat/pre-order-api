<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->user()->isAdmin()) {
            abort(response()->json(
                [
                    'message' => 'Unauthorized',
                ], 403));
        }

        return $next($request);
    }
}

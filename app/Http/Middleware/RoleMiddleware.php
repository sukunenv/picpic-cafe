<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('role:admin,owner')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Akses ditolak. Kamu tidak memiliki izin untuk mengakses resource ini.'
        ], 403);
    }
}

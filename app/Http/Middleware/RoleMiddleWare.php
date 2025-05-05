<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RoleMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => "Unauthorized - Please log in",
            ], 401);
        }

        if (!in_array(Auth::user()->role, $roles)) {
            return response()->json([
                'message' => "Forbidden - You don't have permission",
            ], 403);
        }



        return $next($request);
    }
}

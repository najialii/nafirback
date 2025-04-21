<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleOrPermission
{
    public function handle(Request $request, Closure $next, ...$rolesOrPermissions): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'you are authinticated'], 401);
        }

        foreach ($rolesOrPermissions as $item) {
            if ($user->hasRole($item) || $user->can($item)) {
                return $next($request);
            }
        }

        return response()->json(['message' => ' you are not authorized '], 403);
    }
}

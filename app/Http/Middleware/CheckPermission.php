<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = Auth::user();

        // superadmin otomatis bisa semua
        if ($user && $user->roleData && $user->roleData->name === 'superadmin') {
            return $next($request);
        }

        $permissions = $user->roleData->permissions ?? [];

        if (!is_array($permissions)) {
            $permissions = json_decode($permissions, true);
        }

        if (!$user || !$user->roleData || !in_array($permission, $permissions)) {
            abort(403, 'Unauthorized: Permission denied.');
        }
        
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;

class Permission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->user();

        if (!$user || !$user->relatedRole || !in_array($permission, $user->relatedRole->permissions ?? [])) {
            $request->session()->flash('error', 'Unauthorized action.');
            return redirect()->back();
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIsAdmin
{
    public function handle($request, Closure $next)
    {
        $permission = Auth::user()->getRoleNames()->toArray();
        if(in_array("super-admin", $permission) || in_array("admin", $permission)) {
            return $next($request);
        }
        else {
            return response()->json([
                'status' => 'error',
                'errors' => 'You do not have permission to access',
            ], 403);
        }
    }
}
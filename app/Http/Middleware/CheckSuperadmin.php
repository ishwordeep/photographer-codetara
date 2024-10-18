<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperadmin
{

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Only SUPERADMIN is allowed to perform this action'
            ], 403);
        }

        return $next($request);
    }
}

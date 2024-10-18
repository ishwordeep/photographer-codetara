<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperadminOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->role === 'superadmin' || Auth::user()->role === 'admin')) {
            return $next($request); // Proceed if the user is either superadmin or admin
        }

        // Return forbidden response if neither condition is true
        return response()->json([
            'success' => false,
            'message' => 'Only SUPERADMIN or ADMIN is allowed to perform this action'
        ], 403);
    }
}

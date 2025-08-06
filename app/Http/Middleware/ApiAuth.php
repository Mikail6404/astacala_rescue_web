<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;

class ApiAuth
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  string|null  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Check if user is authenticated
        if (! $this->authService->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            return redirect('/Login')->with('error', 'Please login to access this page.');
        }

        // Check role if specified
        if ($role && ! $this->authService->hasRole($role)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}

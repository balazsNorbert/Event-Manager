<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();
        if ($user && $user->role === 'agent') {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized. Only agents can access this.'], 403);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$scopes): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->user()->hasRoleScope($scopes)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki akses ke area ini.');
    }
}

<?php

namespace App\Bot\Middleware;

use Closure;

class Localhost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if ($request->ip() != "127.0.0.1" && $request->ip() != "::1") {
			abort(403);
		}
        return $next($request);
    }
}

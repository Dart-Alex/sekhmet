<?php

namespace App\Bot\Middleware;

use Closure;
use Carbon\Carbon;

class BotAlive
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
		Cache::put('bot-alive', Carbon::now());
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Carbon\Carbon;
use Cache;

class LogLastUserActivity
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
        if(Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(5);
            Cache::put('vchip:online_user-' . Auth::user()->id, true, $expiresAt);
            $expiresChatAt = Carbon::now()->addMinutes(60);
            if('ceo@vchiptech.com' == Auth::user()->email){
                Cache::put('vchip:chatAdminLive', true, $expiresChatAt);
            }
        }
        return $next($request);
    }
}
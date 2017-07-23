<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\ClientHomePage;

class RedirectIfClient
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = 'client')
	{	$subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();

	    if(!is_object($subdomain)) {
	        return redirect('/');
	    }
	    return $next($request);
	}
}
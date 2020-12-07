<?php

namespace PhuongNam\UserAndPermission\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLoginFirstTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_null(auth('phuongnam')->user()->latest_login)) {
            return redirect()->route('change-password');
        }

        return $next($request);
    }
}

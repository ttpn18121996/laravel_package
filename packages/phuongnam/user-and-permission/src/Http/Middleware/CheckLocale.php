<?php

namespace PhuongNam\UserAndPermission\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class CheckLocale
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
        if (auth('phuongnam')->check()
            && ! is_null(auth('phuongnam')->user()->setting)
            && ! App::isLocale(auth('phuongnam')->user()->setting->language)
        ) {
            App::setlocale(auth('phuongnam')->user()->setting->language);
        }

        return $next($request);
    }
}

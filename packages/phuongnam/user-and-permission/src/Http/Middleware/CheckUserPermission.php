<?php

namespace PhuongNam\UserAndPermission\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PhuongNam\UserAndPermission\Models\SessionToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (! $this->checkToken()) {
            $user = DB::table('users')->where('id', auth('phuongnam')->id())->whereNull('deleted_at')->where('is_active', 1)->first();
            if (is_null($user)) {
                return redirect()->route('logout');
            }

            app(\PhuongNam\UserAndPermission\Repositories\User\User::class)->setUserPermission();
            $token = (new SessionToken)->getToken(auth('phuongnam')->id())->token;
            $request->session()->put('user_token', $token);
        }

        $permissions = $request->session()->get('user_permissions', function () {
            return [];
        });

        if (! auth('phuongnam')->user()->is_admin && ! in_array($permission, $permissions)) {
            abort(403);
        }

        return $next($request);
    }

    /**
     * Kiểm tra token của user hiện tại có trùng khớp không.
     *
     * @return bool
     */
    public function checkToken()
    {
        $sessionToken = new SessionToken;

        return $sessionToken->checkUserToken(auth('phuongnam')->id(), session()->get('user_token'));
    }
}

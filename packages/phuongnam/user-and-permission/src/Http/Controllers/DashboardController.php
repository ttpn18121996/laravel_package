<?php

namespace PhuongNam\UserAndPermission\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use PhuongNam\UserAndPermission\Models\SessionToken;
use PhuongNam\UserAndPermission\Repositories\User\User;

class DashboardController extends Controller
{
    /**
     * @var \PhuongNam\UserAndPermission\Repositories\User\User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Trang chủ sau khi đăng nhập thành công.
     *
     * @return \Illuminate\Http\Request
     */
    public function __invoke()
    {
        $sessionToken = new SessionToken;
        $checkToken = $sessionToken->checkUserToken(auth('phuongnam')->id(), session()->get('user_token'));
        if (! $checkToken) {
            $this->user->setUserPermission();
            $token = $sessionToken->getToken(auth('phuongnam')->id())->token;
            session(['user_token' => $token]);
        }

        return view('userandpermission::index');
    }
}

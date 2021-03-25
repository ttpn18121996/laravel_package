<?php

namespace PhuongNam\UserAndPermission\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhuongNam\UserAndPermission\Repositories\User\User;
use PhuongNam\UserAndPermission\Models\Permission;
use PhuongNam\UserAndPermission\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * @var \PhuongNam\UserAndPermission\Repositories\User\User
     */
    private $user;

    /**
     * @var \PhuongNam\UserAndPermission\Models\Permission
     */
    private $permission;

    public function __construct(User $user, Permission $permission)
    {
        $this->user = $user;
        $this->permission = $permission;
        $this->middleware('guest:phuongnam')->except('logout');
    }

    /**
     * Hiển thị trang đăng nhập.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('phuongnam_userandpermission::auth.login');
    }

    /**
     * Xử lý đăng nhập.
     *
     * @param  \PhuongNam\UserAndPermission\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $result = $this->user->handleLogin($credentials);

        if ($result['status'] === 200) {
            $this->user->setUserPermission();
            $this->user->saveSessionToken();

            if ($result['data'] === 'first time' || $result['message'] === 'first time') {
                return redirect()->route('change-password');
            }

            return redirect()->route('home');
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Xử lý đăng xuất.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Lấy yêu cầu xác thực hiện tại.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('phuongnam');
    }
}

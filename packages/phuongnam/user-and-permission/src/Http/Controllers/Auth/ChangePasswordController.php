<?php

namespace PhuongNam\UserAndPermission\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use PhuongNam\UserAndPermission\Rules\PasswordStrength;

class ChangePasswordController extends Controller
{
    /**
     * Hiển thị trang đổi mật khẩu.
     *
     * @return \Illuminate\Http\Response
     */
    public function showChangePasswordPage()
    {
        if (! auth('phuongnam')->check()) {
            return redirect()->route('login');
        }

        return view('userandpermission::auth.change-password');
    }

    /**
     * Xử lý đổi mật khẩu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current-password' => ['required', 'max:32'],
            'password' => ['required', 'confirmed', new PasswordStrength],
        ]);

        if (! auth('phuongnam')->check()) {
            return redirect()->route('login');
        }

        if (! Hash::check($data['current-password'], auth('phuongnam')->user()->password)) {
            return back()->with('status', __('message.the_current_password_invalid'));
        }

        if ($data['current-password'] == $data['password']) {
            return back()->with('status', __('message.new_old_pass_must_diff'));
        }

        $user = auth('phuongnam')->user();
        $user->password = Hash::make($data['password']);

        if (is_null($user->latest_login)) {
            $user->latest_login = now();
        }

        $user->save();

        return redirect()->route('home');
    }
}

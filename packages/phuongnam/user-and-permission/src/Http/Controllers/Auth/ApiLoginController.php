<?php

namespace PhuongNam\UserAndPermission\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhuongNam\UserAndPermission\Repositories\User\User;
use PhuongNam\UserAndPermission\Http\Requests\LoginRequest;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Client;
use PhuongNam\UserAndPermission\Models\History;

class ApiLoginController extends Controller
{
    /**
     * @var \PhuongNam\UserAndPermission\Repositories\User\User
     */
    private $user;

    /**
     * @var \PhuongNam\UserAndPermission\Models\History
     */
    private $history;

    public function __construct(User $user, History $history)
    {
        $this->user = $user;
        $this->history = $history;
    }

    /**
     * Xử lý đăng nhập.
     *
     * @param  \PhuongNam\UserAndPermission\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $result = $this->user->handleLogin($credentials);

        if ($result['status'] === 200) {
            return nRes([
                'user' => auth('phuongnam')->user(),
                'tokens' => $this->getTokenAndRefreshToken($credentials['username'], $credentials['password'])
            ], $result['message']);
        }

        return nRes()->res401($result['message'], $result['data']);
    }

    /**
     * Lấy token và refresh token.
     *
     * @param  string  $username
     * @param  string  $password
     * @return array
     */
    private function getTokenAndRefreshToken($username, $password)
    {
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;
        $response = $http->post(route('passport.token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $username,
                'password' => $password,
                'scope' => '*',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Refresh token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        $refresh_token = $request->header('Refresh-Token');
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;

        try {
            $response = $http->post(route('passport.token'), [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id' => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'scope' => '*',
                ],
            ]);
            return nRes(json_decode((string) $response->getBody(), true));
        } catch (\Exception $e) {
            return nRes()->res401();
        }
    }

    /**
     * Xử lý đăng xuất.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return nRes(null, __('userandpermission::message.success'));
    }

    /**
     * Xử lý tài khoản chưa đăng nhập.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorized(Request $request)
    {
        if ($request->wantsJson()) {
            return nRes()->res401();
        }

        return redirect()->route('login');
    }
}

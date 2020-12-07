<?php

namespace PhuongNam\UserAndPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginFailed extends Model
{
    protected $table = 'login_failed';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Đếm tài khoản đã đăng nhập thất bại bao nhiêu lần.
     *
     * @param  string  $username
     * @return int
     */
    public function countLoginFailed($username)
    {
        $this->clearXDayHistory($username);

        return $this->where('username', $username)->count();
    }

    /**
     * Lưu thông tin tài khoản đăng nhập thất bại.
     *
     * @param  string  $username
     * @return void
     */
    public function isLoginFailed($username)
    {
        $this->username = $username;
        $this->ip_address = \Request::ip();
        $this->save();
    }

    /**
     * Xóa lịch sử sau x tháng (mặc định là 1 tháng).
     *
     * @param  string  $username
     * @param  int  $x
     * @return void
     */
    public function clearXMonthHistory($username, $x = 1)
    {
        $this->where('username', $username)->where('created_at', '<', Carbon::now()->subMonth($x))->delete();
    }

    /**
     * Xóa lịch sử sau x ngày (mặc định là 1 ngày).
     *
     * @param  string  $username
     * @param  int  $x
     * @return void
     */
    public function clearXDayHistory($username, $x = 1)
    {
        $this->where('username', $username)->where('created_at', '<', Carbon::now()->subDay($x))->delete();
    }
}

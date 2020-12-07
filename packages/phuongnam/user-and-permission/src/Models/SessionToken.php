<?php

namespace PhuongNam\UserAndPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SessionToken extends Model
{
    protected $table = 'session_token';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'token'];
    public $timestamps = false;

    /**
     * Tạo hoặc cập nhật token cho tài khoản người dùng.
     *
     * @param  array|int|null  $userId
     * @return mixed
     */
    public function addToken($userId = null)
    {
        if ($userId != null) {
            if (is_array($userId)) {
                return $this->whereIn('user_id', $userId)->update(['token' => Str::random(64)]);
            }

            return $this->updateOrCreate(
                ['user_id' => $userId],
                ['token' => Str::random(64)]
            );
        }

        return DB::table($this->table)->update(['token' => Str::random(64)]);
    }

    /**
     * Kiểm tra token của user hiện tại có trùng khớp không
     *
     * @param  int  $userId
     * @param  string  $token
     * @return bool
     */
    public function checkUserToken($userId, $token)
    {
        $result = $this->where('user_id', $userId)
            ->where('token', $token)
            ->first();

        return (! is_null($result));
    }

    /**
     * Lấy token mới của user
     *
     * @param  int  $userId
     * @return \PhuongNam\UserAndPermission\Models\SessionToken
     */
    public function getToken($userId)
    {
        return $this->where('user_id', $userId)
            ->select('token')
            ->first();
    }
}

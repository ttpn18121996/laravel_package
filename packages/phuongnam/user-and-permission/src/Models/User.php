<?php

namespace PhuongNam\UserAndPermission\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use PhuongNam\UserAndPermission\Helpers\NModelTrait;

class User extends Authenticatable
{
    use HasApiTokens, NModelTrait, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'username', 'password', 'email', 'is_admin', 'is_active', 'latest_login'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Thiết lập tài khoản khi lưu vào model.
     *
     * @param  string  $value
     * @return void
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower($value);
    }

    /**
     * Thiết lập email khi lưu vào model.
     *
     * @param  string  $value
     * @return void
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Lấy danh sách tài khoản người dùng.
     *
     * @param  array  $filter
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getList(array $filter = [])
    {
        $builder = $this->where('is_admin', 0)
            ->where($this->primaryKey, '<>', auth('phuongnam')->id())
            ->select('id', 'name', 'username', 'email', 'is_admin', 'is_active', 'latest_login', 'updated_at');

        if (auth('phuongnam')->user()->is_admin) {
            $builder->withTrashed()->addSelect('deleted_at');
        }

        $builder = $this->searchBuilder($builder, $filter, ['is_active']);
        $builder = $this->sortBuilder($builder, $filter);

        return $this->getPaginate($builder, $filter);
    }

    /**
     * Lấy thông tin tài khoản người dùng.
     *
     * @param  int  $id
     * @return \Modules\UserAndPermission\Models\User
     */
    public function getDetail($id)
    {
        $detail = $this->where($this->primaryKey, $id)
            ->where('is_admin', 0)
            ->select(
                'id',
                'name',
                'username',
                'email',
                'is_admin',
                'is_active',
                'latest_login',
                'created_at',
                'updated_at'
            );

        if (auth('phuongnam')->user()->is_admin) {
            $detail->withTrashed()->addSelect('deleted_at');
        }

        return $detail->first();
    }

    /**
     * Cập nhật lần đăng nhập gần nhất.
     *
     * @param  int  $id
     * @return void
     */
    public function updateLatestLogin($id)
    {
        $user = $this->where($this->primaryKey, $id)->update(['latest_login' => now()]);
    }

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \Modules\UserAndPermission\Models\User
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Các nhóm quyền của tài khoản người dùng.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_groups', 'user_id', 'group_id');
    }

    /**
     * Các quyền của tài khoản người dùng.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    /**
     * Các thông tin cài đặt của tài khoản.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function setting()
    {
        return $this->hasOne(UserSetting::class, 'user_id', 'id');
    }

    /**
     * Lấy danh sách tài khoản người dùng và kiểm tra tài khoản người dùng của nhóm quyền.
     *
     * @param  \Illuminate\Support\Collection|array  $listUsersSelected
     * @return \Illuminate\Support\Collection
     */
    public function getForEdit($listUsersSelected)
    {
        if ($listUsersSelected instanceof \Illuminate\Support\Collection) {
            $listUsersSelected = $listUsersSelected->toArray();
        }

        return $this->getListAndCheckSelected($this, [
            'list' => $listUsersSelected,
            'columns' => ['id', 'username', 'name', 'email'],
            'wheres' => [['is_admin', '<>', 1]],
        ]);
    }
}

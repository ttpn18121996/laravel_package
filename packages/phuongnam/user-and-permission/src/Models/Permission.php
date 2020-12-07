<?php

namespace PhuongNam\UserAndPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhuongNam\UserAndPermission\Helpers\NModelTrait;

class Permission extends Model
{
    use NModelTrait;

    protected $table = 'permissions';
    protected $primaryKey = 'id';

    /**
     * Lấy danh sách quyền và kiểm tra quyền đã chọn.
     *
     * @param  \Illuminate\Support\Collection|arrayarray  $listUserPermissions
     * @return \Illuminate\Support\Collection
     */
    public function getForEdit($listPermissionsSelected)
    {
        if ($listPermissionsSelected instanceof \Illuminate\Support\Collection) {
            $listPermissionsSelected = $listPermissionsSelected->toArray();
        }

        return $this->getListAndCheckSelected($this, [
            'list' => $listPermissionsSelected,
            'column' => ['id', 'name', 'code'],
        ]);
    }

    /**
     * Lấy danh sách quyền của tài khoản
     *
     * @param  int  $user_id
     * @return array
     */
    public function getUserPermission($user_id)
    {
        $groupPermissions = DB::table('group_permissions as gp')
            ->join('permissions as per', 'per.id', '=', 'gp.permission_id')
            ->whereIn('gp.group_id', function ($query) use ($user_id) {
                $query->from('user_groups as ug')
                ->where('ug.user_id', $user_id)
                ->select('ug.group_id');
            })
            ->select('per.code');
        $permissions = DB::table('user_permissions as up')
            ->join('permissions as per', 'per.id', '=', 'up.permission_id')
            ->where('up.user_id', $user_id)
            ->select('per.code')
            ->union($groupPermissions)
            ->get();

        return $permissions->pluck('code')->toArray();
    }
}

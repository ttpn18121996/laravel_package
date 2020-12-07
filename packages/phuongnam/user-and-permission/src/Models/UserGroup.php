<?php

namespace PhuongNam\UserAndPermission\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = 'user_groups';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Xóa nhóm quyền khỏi tài khoản người dùng | Xóa tài khoản người dùng khỏi nhóm quyền.
     *
     * @param  int  $id
     * @param  string  $type (user_group: user in group, group_user: group in user)
     * @return void
     */
    public function remove($id, $type = 'user_group')
    {
        if ($type == 'user_group') {
            return $this->where('group_id', $id)->delete();
        }

        return $this->where('user_id', $id)->delete();
    }
}

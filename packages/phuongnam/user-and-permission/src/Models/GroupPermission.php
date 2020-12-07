<?php

namespace PhuongNam\UserAndPermission\Models;

use Illuminate\Database\Eloquent\Model;

class GroupPermission extends Model
{
    protected $table = 'group_permissions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Thêm quyền cho nhóm quyền.
     *
     * @param  array  $data
     * @return void
     */
    public function adds(array $data)
    {
        $this->insert($data);
    }

    /**
     * Xóa quyền khỏi nhóm quyền.
     *
     * @param  int  $group_id
     * @return bool
     */
    public function remove($group_id)
    {
        $history = new History;
        $history->add($this->table, '%s đã cập nhật một số quyền vào <a href="/dashboard_admin_23644466/group/'.$group_id.'">nhóm quyền</a> có id = '.$group_id);

        return $this->where('group_id', $group_id)->delete();
    }
}

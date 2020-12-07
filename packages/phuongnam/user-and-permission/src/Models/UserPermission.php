<?php

namespace PhuongNam\UserAndPermission\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $table = 'user_permissions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Lấy danh sách quyền.
     *
     * @param  array  $filter
     * @return mixed
     */
    public function getList(array $filter = [])
    {
        $builder = $this->join('permissions as p', 'p.id', '=', 'permission_id')
            ->join('users as u', 'u.id', '=', 'user_id')
            ->select(
                $this->table.'.id',
                'p.name',
                'p.description',
                'permission_id',
                'user_id'
            );

        if (isset($filter['user_id'])) {
            $builder->where('user_id', $filter['user_id']);
        }

        return $this->getPaginate($builder, $filter);
    }

    /**
     * Xóa quyền khỏi user.
     *
     * @param  int  $user_id
     * @return bool
     */
    public function remove($user_id)
    {
        return $this->where('user_id', $user_id)->delete();
    }
}

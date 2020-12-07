<?php

namespace PhuongNam\UserAndPermission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhuongNam\UserAndPermission\Helpers\NModelTrait;

class Group extends Model
{
    use NModelTrait;

    protected $table = 'groups';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'description', 'created_by', 'updated_by',
    ];

    /**
     * Lấy danhs sách nhóm quyền.
     *
     * @param  array  $filter
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getList(array $filter = [])
    {
        $builder = $this->select('id', 'name', 'description', 'created_at', 'updated_at');

        $builder = $this->searchBuilder($builder, $filter);
        $builder = $this->sortBuilder($builder, $filter);

        return $this->getPaginate($builder, $filter);
    }

    /**
     * Lấy chi tiết nhóm quyền.
     *
     * @param  int  $id
     * @return \PhuongNam\UserAndPermission\Models\Group
     */
    public function getDetail($id)
    {
        $result = $this->leftJoin('users as cr', 'cr.id', '=', $this->table.'.created_by')
            ->leftJoin('users as up', 'up.id', '=', $this->table.'.updated_by')
            ->where($this->table.'.'.$this->primaryKey, $id)
            ->select(
                $this->table.'.id',
                $this->table.'.name',
                $this->table.'.description',
                $this->table.'.created_at',
                $this->table.'.updated_at',
                $this->table.'.created_by',
                DB::raw('cr.name as created_by_name'),
                $this->table.'.updated_by',
                DB::raw('up.name as updated_by_name')
            );

        return $result->first();
    }

    /**
     * Thêm nhóm quyền.
     *
     * @param  array  $data
     * @return \PhuongNam\UserAndPermission\Models\Group
     */
    public function add(array $data)
    {
        $this->name = strip_tags($data['name']);
        $this->description = isset($data['description']) ? strip_tags($data['description']) : null;
        $this->created_by = auth('phuongnam')->id();
        $this->updated_by = auth('phuongnam')->id();

        return $this->save();
    }

    /**
     * Các tài khoản người dùng thuộc nhóm quyền.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_groups', 'group_id', 'user_id');
    }

    /**
     * Các quyền của tài khoản người dùng.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'group_permissions', 'group_id', 'permission_id');
    }

    /**
     * Lấy danh sách nhóm quyền và kiểm tra nhóm quyền của tài khoản người dùng.
     *
     * @param  \Illuminate\Support\Collection|array  $listGroupsSelected
     * @return \Illuminate\Support\Collection
     */
    public function getForEdit($listGroupsSelected)
    {
        if ($listGroupsSelected instanceof \Illuminate\Support\Collection) {
            $listGroupsSelected = $listGroupsSelected->toArray();
        }

        return $this->getListAndCheckSelected($this, [
            'list' => $listGroupsSelected,
            'columns' => ['id', 'name', 'description'],
        ]);
    }

}

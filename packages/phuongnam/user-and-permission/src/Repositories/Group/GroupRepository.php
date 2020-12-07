<?php

namespace PhuongNam\UserAndPermission\Repositories\Group;

use Illuminate\Support\Facades\DB;
use PhuongNam\UserAndPermission\Models\Group as GroupModel;
use PhuongNam\UserAndPermission\Models\GroupPermission;
use PhuongNam\UserAndPermission\Models\History;
use PhuongNam\UserAndPermission\Models\Permission;
use PhuongNam\UserAndPermission\Models\SessionToken;
use PhuongNam\UserAndPermission\Models\User;
use PhuongNam\UserAndPermission\Models\UserGroup;

class GroupRepository implements Group
{
    /**
     * @var \PhuongNam\UserAndPermission\Models\Group
     */
    private $group;

    /**
     * @var \PhuongNam\UserAndPermission\Models\History
     */
    protected $history;

    /**
     * @var \PhuongNam\UserAndPermission\Models\User
     */
    protected $user;

    /**
     * @var \PhuongNam\UserAndPermission\Models\Permission
     */
    protected $permission;

    /**
     * @var \PhuongNam\UserAndPermission\Models\UserGroup
     */
    protected $userGroup;

    /**
     * @var \PhuongNam\UserAndPermission\Models\GroupPermission
     */
    protected $groupPermission;

    /**
     * @var \PhuongNam\UserAndPermission\Models\SessionToken
     */
    protected $sessionToken;

    public function __construct(
        GroupModel $group,
        History $history,
        User $user,
        Permission $permission,
        UserGroup $userGroup,
        GroupPermission $groupPermission,
        SessionToken $sessionToken
    ) {
        $this->group = $group;
        $this->history = $history;
        $this->user = $user;
        $this->permission = $permission;
        $this->userGroup = $userGroup;
        $this->groupPermission = $groupPermission;
        $this->sessionToken = $sessionToken;
    }

    /**
     * Lấy danh sách tất cả nhóm quyền.
     *
     * @param  array  $filter
     * @return array
     */
    public function getListAll(array $filter = [])
    {
        $filter['per_page'] = 0;
        $groups = $this->group->getList($filter);

        return [
            'filter' => $filter,
            'data' => $groups
        ];
    }

    /**
     * Lấy danh sách nhóm quyền có phân trang.
     *
     * @param  array  $filter
     * @return array
     */
    public function getListPagination(array $filter = [])
    {
        if (isset($filter['per_page']) && $filter['per_page'] <= 0) {
            return collect([]);
        }

        $groups = $this->group->getList($filter);

        return [
            'filter' => $filter,
            'data' => $groups
        ];
    }

    /**
     * Lấy thông tin nhóm quyền.
     *
     * @param  int  $id
     * @return array
     */
    public function getDetail($id)
    {
        $status = 404;
        $group = $this->group->getDetail($id);

        if (! is_null($group)) {
            $status = 200;
            $users = $this->user->getForEdit($group->users->pluck('id')->toArray());
            $permissions = $this->permission->getForEdit($group->permissions->pluck('id')->toArray());
        }

        return [
            'status' => $status,
            'data' => $group,
            'users' => $users ?? [],
            'permissions' => $permissions ?? [],
        ];
    }

    /**
     * Thêm nhóm quyền.
     *
     * @param  array  $data
     * @return array
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $this->group->name = strip_tags($data['name']);
            $this->group->description = strip_tags($data['description']);
            $this->group->created_by = auth('phuongnam')->id();
            $this->group->updated_by = auth('phuongnam')->id();

            $this->group->save();

            if (isset($data['user_ids'])) {
                $this->group->users()->sync($data['user_ids']);
            }

            if (isset($data['permission_ids'])) {
                $this->group->permissions()->sync($data['permission_ids']);
            }

            DB::commit();

            return [
                'status' => 201,
                'message' => __('message.create_success', ['attribute' => 'group']),
                'data' => $this->group,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 400,
                'message' => __('message.create_failed', ['attribute' => 'group']),
                'data' => $e->getMessage()
            ];
        }
    }

    /**
     * Cập nhật nhóm quyền.
     *
     * @param  array  $data
     * @param  int  $id
     * @return array
     */
    public function update(array $data, $id)
    {
        $data['user_ids'] = $data['user_ids'] ?? [];
        $data['permission_ids'] = $data['permission_ids'] ?? [];

        try {
            DB::beginTransaction();

            $group = GroupModel::findOrFail($id);
            $group->name = strip_tags($data['name']);
            $group->description = strip_tags($data['description']);

            $group->save();

            $this->updateUserForGroup($group, $data['user_ids']);
            $this->updatePermissionForGroup($group, $data['permission_ids']);

            DB::commit();

            return [
                'status' => 200,
                'message' => __('message.update_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 400,
                'message' => __('message.update_failed'),
                'data' => $e->getMessage()
            ];
        }
    }

    /**
     * Cập nhật tài khoản cho nhóm quyền.
     *
     * @param  \PhuongNam\UserAndPermission\Models\Group  $group
     * @param  array  $users
     * @return void
     */
    private function updateUserForGroup(GroupModel $group, array $users)
    {
        $beforeChange = $group->users->pluck('id')->all();

        if (count(array_diff($beforeChange, $users)) || count(array_diff($users, $beforeChange))) {
            $group->users()->sync($users);
            $this->sessionToken->addToken($group->id);
        }
    }

    /**
     * Cập nhật quyền cho nhóm quyền.
     *
     * @param  \PhuongNam\UserAndPermission\Models\Group  $group
     * @param  array  $permissions
     * @return void
     */
    private function updatePermissionForGroup(GroupModel $group, array $permissions)
    {
        $beforeChange = $group->permissions->pluck('id')->all();

        if (count(array_diff($beforeChange, $permissions)) || count(array_diff($permissions, $beforeChange))) {
            $group->permissions()->sync($permissions);
            $this->sessionToken->addToken($group->id);
        }
    }

    /**
     * Xóa tài khoản người dùng khỏi nhóm quyền.
     *
     * @param  int  $groupId
     * @return void
     */
    public function removeUsersInGroup($groupId)
    {
        $this->userGroup->remove($groupId, 'user_group');
    }

    public function removePermissionsInGroup($groupId)
    {
        $this->groupPermission->remove($groupId);
    }

    /**
     * Xóa nhóm quyền.
     *
     * @param int  $id
     * @return array
     */
    public function delete($id)
    {
        $group = $this->group->where('id', $id)->firstOrFail();

        $result = $group->delete();

        if ($result) {
            $this->removeUsersInGroup($id);
            $this->removePermissionsInGroup($id);
            $this->sessionToken->addToken($id);

            return [
                'status' => 200,
                'message' => __('message.delete_success'),
            ];
        }

        return [
            'status' => 400,
            'message' => __('message.delete_failed'),
        ];
    }
}

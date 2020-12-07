<?php

namespace PhuongNam\UserAndPermission\Repositories\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhuongNam\UserAndPermission\Models\Group;
use PhuongNam\UserAndPermission\Models\History;
use PhuongNam\UserAndPermission\Models\LoginFailed;
use PhuongNam\UserAndPermission\Models\Permission;
use PhuongNam\UserAndPermission\Models\SessionToken;
use PhuongNam\UserAndPermission\Models\User as UserModel;
use PhuongNam\UserAndPermission\Models\UserGroup;
use PhuongNam\UserAndPermission\Models\UserPermission;

class UserRepository implements User
{
    /**
     * @var \PhuongNam\UserAndPermission\Models\User
     */
    protected $user;

    /**
     * @var \PhuongNam\UserAndPermission\Models\History
     */
    protected $history;

    /**
     * @var \PhuongNam\UserAndPermission\Models\Group
     */
    protected $group;

    /**
     * @var \PhuongNam\UserAndPermission\Models\Permission
     */
    protected $permission;

    /**
     * @var \PhuongNam\UserAndPermission\Models\UserGroup
     */
    protected $userGroup;

    /**
     * @var \PhuongNam\UserAndPermission\Models\UserPermission
     */
    protected $userPermission;

    /**
     * @var \PhuongNam\UserAndPermission\Models\SessionToken
     */
    protected $sessionToken;

    public function __construct(
        UserModel $user,
        History $history,
        Group $group,
        Permission $permission,
        UserGroup $userGroup,
        UserPermission $userPermission,
        SessionToken $sessionToken
    ) {
        $this->user = $user;
        $this->history = $history;
        $this->group = $group;
        $this->permission = $permission;
        $this->userGroup = $userGroup;
        $this->userPermission = $userPermission;
        $this->sessionToken = $sessionToken;
    }

    /**
     * Lấy danh sách tất cả tài khoản người dùng.
     *
     * @param  $arg
     * @return array
     */
    public function getListAll(array $filter = [])
    {
        $filter['per_page'] = 0;
        $users = $this->user->getList($filter);

        return [
            'filter' => $filter,
            'data' => $users
        ];
    }

    /**
     * Lấy danh sách tài khoản người dùng có phân trang.
     *
     * @param  array  $filter
     * @return array
     */
    public function getListPagination(array $filter = [])
    {
        if (isset($filter['per_page']) && $filter['per_page'] <= 0) {
            return collect([]);
        }

        $users = $this->user->getList($filter);

        return [
            'filter' => $filter,
            'data' => $users
        ];
    }

    /**
     * Lấy thông tin tài khoản người dùng.
     *
     * @param  int  $id
     * @return array
     */
    public function getDetail($id)
    {
        $status = 404;
        $message = __('message.failed');
        $user = $this->user->getDetail($id);

        if (! is_null($user)) {
            $status = 200;
            $message = __('message.success');
            $groups = $this->group->getForEdit($user->groups->pluck('id'));
            $permissions = $this->permission->getForEdit($user->permissions->pluck('id'));
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $user,
            'groups' => $groups ?? [],
            'permissions' => $permissions ?? [],
        ];
    }

    /**
     * Thêm tài khoản người dùng.
     *
     * @param  array  $data
     * @return array
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $this->user->username = $data['username'];
            $this->user->email = $data['email'];
            $this->user->name = strip_tags($data['name']);
            $this->user->password = bcrypt($data['password']);

            $this->user->save();

            if (isset($data['group_ids'])) {
                $this->user->groups()->sync($data['group_ids']);
            }

            if (isset($data['permission_ids'])) {
                $this->user->permissions()->sync($data['permission_ids']);
            }

            DB::commit();

            return [
                'status' => 201,
                'message' => __('message.create_success', ['attribute' => 'user']),
                'data' => $this->user,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 406,
                'message' => __('message.create_failed', ['attribute' => 'user']),
                'data' => $e->getMessage()
            ];
        }
    }

    /**
     * Cập nhật tài khoản người dùng.
     *
     * @param  array  $data
     * @param  int  $id
     * @return array
     */
    public function update(array $data, $id)
    {
        $data['group_ids'] = $data['group_ids'] ?? [];
        $data['permission_ids'] = $data['permission_ids'] ?? [];

        try {
            DB::beginTransaction();

            $user = UserModel::findOrFail($id);
            $user->email = strtolower($data['email']);
            $user->name = strip_tags($data['name']);
            $user->is_active = $data['is_active'];

            $user->save();

            $this->updateGroupForUser($user, $data['group_ids']);
            $this->updatePermissionForUser($user, $data['permission_ids']);

            DB::commit();

            return [
                'status' => 200,
                'message' => __('message.update_success'),
                'data' => null,
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
     * Cập nhật nhóm quyền cho tài khoản người dùng.
     *
     * @param  \PhuongNam\UserAndPermission\Models\User  $user
     * @param  array  $groups
     * @return void
     */
    private function updateGroupForUser(UserModel $user, array $groups)
    {
        $beforeChange = $user->groups->pluck('id')->all();

        if (count(array_diff($beforeChange, $groups)) || count(array_diff($groups, $beforeChange))) {
            $user->groups()->sync($groups);
            $this->sessionToken->addToken($user->id);
        }
    }

    /**
     * Cập nhật quyền cho tài khoản người dùng.
     *
     * @param  \PhuongNam\UserAndPermission\Models\User  $user
     * @param  array  $permissions
     * @return void
     */
    private function updatePermissionForUser(UserModel $user, array $permissions)
    {
        $beforeChange = $user->permissions->pluck('id')->all();

        if (count(array_diff($beforeChange, $permissions)) || count(array_diff($permissions, $beforeChange))) {
            $user->permissions()->sync($permissions);
            $this->sessionToken->addToken($user->id);
        }
    }

    /**
     * Gỡ bỏ nhóm quyền khỏi tài khoản người dùng.
     *
     * @param  int  $userId
     * @return void
     */
    public function removeGroupsInUser($userId)
    {
        $this->userGroup->remove($userId, 'group_user');
    }

    /**
     * Gỡ bỏ quyền khỏi tài khoản người dùng.
     *
     * @param  int  $userId
     * @return void
     */
    public function removePermissionsInUser($userId)
    {
        $this->userPermission->remove($userId);
    }

    /**
     * Cập nhật trạng thái tài khoản người dùng.
     *
     * @param  int  $id
     * @param  int|bool
     * @return void
     */
    public function updateStatus($id, $status)
    {
        $result = $this->user->where('id', $id)->update(['is_active' => (int) $status]);

        if ($result) {
            $this->sessionToken->addToken($id);
            $action = '%s vừa mới cập nhật trạng thái <a href="/dashboard_admin_23644466/user/'.$id.'">người dùng</a> có '.$this->user->getKeyName().' = '.$id.' vào bảng users';
            $this->history->add('users', $action);

            return [
                'status' => 200,
                'message' => __('message.update_success'),
            ];
        }

        return [
            'status' => 404,
            'message' => __('message.update_failed'),
        ];
    }

    /**
     * Xóa tài khoản người dùng.
     *
     * @param  int  $id
     * @return array
     */
    public function delete($id)
    {
        $user = $this->user->where([
            ['id', '=', $id],
            ['id', '<>', auth('phuongnam')->id()],
            ['is_admin', '=', 0],
        ])->firstOrFail();

        $result = $user->delete();

        if ($result) {
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

    /**
     * Xóa vĩnh viễn tài khoản người dùng.
     *
     * @param  int  $id
     * @return array
     */
    public function forceDelete($id)
    {
        if (! auth('phuongnam')->user()->is_admin) {
            return [
                'status' => 403,
                'message' => __('message.delete_failed'),
            ];
        }

        $user = $this->user->where([
            ['id', '=', $id],
            ['id', '<>', auth('phuongnam')->id()],
            ['is_admin', '=', 0],
        ])->withTrashed()->firstOrFail();

        $result = $user->forceDelete();

        if ($result) {
            $this->removeGroupsInUser($id);
            $this->removePermissionsInUser($id);
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

    /**
     * Xử lý khôi phục tài khoản người dùng.
     *
     * @param  int  $id
     * @return array
     */
    public function restore($id)
    {
        if (! auth('phuongnam')->user()->is_admin) {
            return [
                'status' => 403,
                'message' => __('message.restore_failed'),
            ];
        }

        $user = $this->user->where([
            ['id', '=', $id],
            ['id', '<>', auth('phuongnam')->id()],
            ['is_admin', '=', 0],
        ])->withTrashed()->firstOrFail();

        $result = $user->restore();

        if ($result) {
            return [
                'status' => 200,
                'message' => __('message.restore_success'),
            ];
        }

        return [
            'status' => 400,
            'message' => __('message.restore_failed'),
        ];
    }

    /**
     * Xử lý đăng nhập.
     *
     * @param  array  $credentials
     * @return array
     */
    public function handleLogin(array $credentials)
    {
        $loginFailed = new LoginFailed;

        if ($loginFailed->countLoginFailed($credentials['username']) >= 5) {
            return [
                'status' => 401,
                'data' => null,
                'message' => __('message.user_locked_day', ['num' => 24]),
            ];
        }

        if (auth('phuongnam')->attempt($credentials)) {
            $user = auth('phuongnam')->user();

            if ($user->is_active) {
                if (is_null($user->latest_login)) {
                    return [
                        'status' => 200,
                        'message' => 'first time',
                        'data' => 'first time',
                    ];
                }

                $user->updateLatestLogin($user->id);
                $this->history->add('users', '%s đã đăng nhập vào hệ thống.');

                return [
                    'status' => 200,
                    'data' => auth('phuongnam')->user(),
                    'message' => __('message.login_success'),
                ];
            }

            auth('phuongnam')->logout();

            return [
                'status' => 401,
                'data' => $credentials,
                'message' => __('message.user_locked'),
            ];
        }

        $loginFailed->isLoginFailed($credentials['username']);

        return [
            'status' => 401,
            'data' => ['messages' => 'Đăng nhập thất bại!'],
            'message' => __('message.login_failed')
        ];
    }

    /**
     * Thiết lập quyền cho tài khoản sau khi đăng nhập.
     *
     * @return void
     */
    public function setUserPermission()
    {
        if (auth('phuongnam')->check()) {
            session(['user_permissions' => $this->permission->getUserPermission(auth('phuongnam')->id())]);
        }
    }

    /**
     * Lưu token đăng nhập.
     *
     * @return void
     */
    public function saveSessionToken()
    {
        $sessionToken = new SessionToken;
        $token = $sessionToken->addToken(auth('phuongnam')->id())->token;
        session(['user_token' => $token]);
    }
}

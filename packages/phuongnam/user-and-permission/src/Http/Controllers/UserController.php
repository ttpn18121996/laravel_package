<?php

namespace PhuongNam\UserAndPermission\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PhuongNam\UserAndPermission\Http\Requests\UserRequest;
use PhuongNam\UserAndPermission\Models\Permission;
use PhuongNam\UserAndPermission\Repositories\Group\Group;
use PhuongNam\UserAndPermission\Repositories\User\User;

class UserController extends Controller
{
    /**
     * Danh sách trạng thái.
     *
     * @var array
     */
    private $listStatus;

    /**
     * @var \PhuongNam\UserAndPermission\Repositories\User\User
     */
    private $user;

    /**
     * @var \PhuongNam\UserAndPermission\Repositories\Group\Group
     */
    private $group;

    public function __construct(User $user, Group $group)
    {
        $this->user = $user;
        $this->group = $group;
        $this->listStatus = [
            '-1' => __('All'),
            '0' => __('Deactive'),
            '1' => __('Active'),
        ];
    }

    /**
     * Hiển thị danh sách tài khoản người dùng.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->all();
        if (isset($filter['is_active']) && $filter['is_active'] == -1) {
            unset($filter['is_active']);
        }

        $users = $this->user->getListPagination($filter);

        return view('phuongnam_userandpermission::user.index', [
            'filter' => $users['filter'],
            'list' => $users['data'],
            'list_status' => $this->listStatus,
        ]);
    }

    /**
     * Hiển thị form tạo tài khoản người dùng.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $list_groups = $this->group->getListAll()['data']->toArray();
        $list_permissions = Permission::all()->toArray();

        return view('phuongnam_userandpermission::user.create', compact('list_groups', 'list_permissions'));
    }

    /**
     * Xử lý lưu thông tin tài khoản người dùng tạo mới.
     *
     * @param  \PhuongNam\UserAndPermission\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $result = $this->user->create($data);
        $typeSubmit = $request->type_submit ?? 'submit';

        if ($typeSubmit == 'ajax-continue') {
            $redirectTo = route('userandpermission.user.create');
        } elseif ($typeSubmit == 'ajax-quit') {
            $redirectTo = route('userandpermission.user.index');
        } else {
            $redirectTo = route('userandpermission.user.show', ['id' => $result['data']->id]);
        }

        return nRes($redirectTo, $result['message'], $result['status']);
    }

    /**
     * Hiển thị thông tin chi tiết tài khoản người dùng.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->user->getDetail($id);

        if ($result['status'] == 404) {
            abort(404, __('Not found'));
        }

        $detail = $result['data'];
        $list_groups = $detail->groups->toArray();
        $list_permissions = $detail->permissions->toArray();

        return view('phuongnam_userandpermission::user.show', compact('detail', 'list_groups', 'list_permissions'));
    }

    /**
     * Hiển thị form chi tiết chỉnh sửa thông tin tài khoản người dùng.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = $this->user->getDetail($id);

        if ($result['status'] == 404) {
            abort(404, __('Not found'));
        }

        $detail = $result['data'];
        $list_groups = $result['groups'];
        $list_permissions = $result['permissions'];
        $selectAllGroups = $result['groups']->filter(function ($group) {
            return $group['selected'];
        })->count() == $list_groups->count();
        $selectAllPermissions = $result['permissions']->filter(function ($permission) {
            return $permission['selected'];
        })->count() == $list_permissions->count();

        return view('phuongnam_userandpermission::user.edit', compact(
            'detail',
            'list_groups',
            'list_permissions',
            'selectAllGroups',
            'selectAllPermissions'
        ));
    }

    /**
     * Xử lý cập nhật thông tin tài khoản người dùng.
     *
     * @param  \PhuongNam\UserAndPermission\Http\Requests\UserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->user->update($data, $id);
        $typeSubmit = $request->type_submit ?? 'submit';

        if ($typeSubmit == 'ajax-continue') {
            $redirectTo = route('userandpermission.user.edit', ['id' => $id]);
        } elseif ($typeSubmit == 'ajax-quit') {
            $redirectTo = route('userandpermission.user.index');
        } else {
            $redirectTo = route('userandpermission.user.show', ['id' => $id]);
        }

        return nRes($redirectTo, $result['message'], $result['status']);
    }

    /**
     * Ajax cập nhật trạng thái tài khoản người dùng.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $status = $request->only(['is_active']);
        $result = $this->user->updateStatus($id, $status['is_active']);

        return response()->json($result);
    }

    /**
     * Ajax xử lý xóa tài khoản người dùng (đánh dấu xóa).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = $this->user->delete($id);

        return response()->json($result);
    }

    public function restore(Request $request, $id)
    {
        $result = $this->user->restore($id);

        return response()->json($result);
    }

    /**
     * Ajax xử lý xóa vĩnh viễn tài khoản người dùng.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelele($id)
    {
        $result = $this->user->forceDelete($id);

        return response()->json($result);
    }
}

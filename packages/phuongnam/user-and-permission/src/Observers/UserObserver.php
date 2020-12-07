<?php

namespace Modules\UserAndPermission\Observers;

use Modules\UserAndPermission\Models\History;
use Modules\UserAndPermission\Models\User;

class UserObserver
{
    /**
     * @var \Modules\UserAndPermission\Models\History
     */
    private $history;

    public function __construct(History $history)
    {
        $this->history = $history;
    }
    /**
     * Xử lý sự kiện sau khi tạo xong 1 user.
     *
     * @param  \Modules\UserAndPermission\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->history->add(
            $user->getTable(),
            '%s đã thêm 1 <a href="/dashboard_admin_23644466/user/'.$user->id.'">tài khoản người dùng</a> có '.$user->getKeyName().' = '.$user->id.' vào bảng '.$user->getTable().'.'
        );
    }

    /**
     * Xử lý sự kiện sau khi cập nhật user.
     *
     * @param  \Modules\UserAndPermission\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $this->history->add(
            $user->getTable(),
            '%s đã cập nhật 1 <a href="/dashboard_admin_23644466/user/'.$user->id.'">tài khoản người dùng</a> có '.$user->getKeyName().' = '.$user->id.' ở bảng '.$user->getTable().'.'
        );
    }

    /**
     * Xử lý sự kiện sau khi xóa user.
     *
     * @param  \Modules\UserAndPermission\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $this->history->add(
            $user->getTable(),
            '%s đã xóa 1 <a href="/dashboard_admin_23644466/user/'.$user->id.'">tài khoản người dùng</a> có '.$user->getKeyName().' = '.$user->id.' ở bảng '.$user->getTable().'.'
        );
    }

    /**
     * Xử lý sự kiện sau khi xóa cứng user.
     *
     * @param  \Modules\UserAndPermission\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        $this->history->add(
            $user->getTable(),
            '%s đã xóa vĩnh viễn 1 <a href="/dashboard_admin_23644466/user/'.$user->id.'">tài khoản người dùng</a> có '.$user->getKeyName().' = '.$user->id.' ở bảng '.$user->getTable().'.'
        );
    }

    /**
     * Xử lý sự kiện sau khi khôi phục tài khoản.
     *
     * @param  \Modules\UserAndPermission\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        $this->history->add(
            $user->getTable(),
            '%s đã khôi phục 1 <a href="/dashboard_admin_23644466/user/'.$user->id.'">tài khoản người dùng</a> có '.$user->getKeyName().' = '.$user->id.' ở bảng '.$user->getTable().'.'
        );
    }
}

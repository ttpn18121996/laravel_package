<?php

namespace Modules\UserAndPermission\Observers;

use Modules\UserAndPermission\Models\Group;
use Modules\UserAndPermission\Models\History;

class GroupObserver
{
    /**
     * Xử lý sự kiện sau khi tạo xong 1 Group.
     *
     * @param  \Modules\GroupAndPermission\Models\Group  $group
     * @return void
     */
    public function created(Group $group)
    {
        $history = new History();
        $history->add(
            $group->getTable(),
            '%s đã thêm 1 dữ liệu có '.$group->getKeyName().' = '.$group->id.' vào bảng '.$group->getTable().'.'
        );
    }

    /**
     * Xử lý sự kiện sau khi cập nhật Group.
     *
     * @param  \Modules\GroupAndPermission\Models\Group  $group
     * @return void
     */
    public function updated(Group $group)
    {
        # code...
    }

    /**
     * Xử lý sự kiện sau khi xóa Group.
     *
     * @param  \Modules\GroupAndPermission\Models\Group  $group
     * @return void
     */
    public function deleted(Group $group)
    {
        # code...
    }

    /**
     * Xử lý sự kiện sau khi xóa cứng Group.
     *
     * @param  \Modules\GroupAndPermission\Models\Group  $group
     * @return void
     */
    public function forceDeleted(Group $group)
    {
        # code...
    }
}

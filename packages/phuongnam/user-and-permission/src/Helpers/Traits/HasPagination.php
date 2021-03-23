<?php

namespace PhuongNam\UserAndPermission\Helpers\Traits;

trait HasPagination
{
    /**
     * Lấy danh sách và chia trang
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  array  $filter
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWithPagination($builder, array $filter = [])
    {
        $perpage = $filter['per_page'] ?? config('userandpermission.items_per_page', 10);
        $page = $filter['page'] ?? config('userandpermission.page_default', 1);
        $pageName = $filter['pageName'] ?? config('userandpermission.page_name', 'page');

        if ($perpage <= 0) {
            return $builder->get();
        }

        return $builder->paginate($perpage, $columns = ['*'], $pageName, $page);
    }
}

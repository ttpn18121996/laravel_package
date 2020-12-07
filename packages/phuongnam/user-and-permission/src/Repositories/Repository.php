<?php

namespace PhuongNam\UserAndPermission\Repositories;

interface Repository
{
    /**
     * Lấy danh sách tất cả tài nguyên.
     *
     * @param  array  $filter
     * @return array
     */
    public function getListAll(array $filter = []);

    /**
     * Lấy danh sách tài nguyên có phân trang.
     *
     * @param  array  $filter
     * @return array
     */
    public function getListPagination(array $filter = []);

    /**
     * Lấy thông tin tài nguyên.
     *
     * @param  int  $id
     * @return array
     */
    public function getDetail($id);

    /**
     * Thêm tài nguyên.
     *
     * @param  array  $data
     * @return array
     */
    public function create(array $data);

    /**
     * Cập nhật tài nguyên.
     *
     * @param  array  $data
     * @param  int  $id
     * @return array
     */
    public function update(array $data, $id);

    /**
     * Xóa tài nguyên.
     *
     * @param int  $id
     * @return array
     */
    public function delete($id);
}

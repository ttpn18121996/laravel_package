<?php

namespace PhuongNam\UserAndPermission\Repositories\User;

use PhuongNam\UserAndPermission\Repositories\Repository;

interface User extends Repository
{
    /**
     * Xử lý đăng nhập.
     *
     * @param  array  $credentials
     * @return array
     */
    public function handleLogin(array $credentials);
}

<?php

use PhuongNam\UserAndPermission\Helpers\NName;
use PhuongNam\UserAndPermission\Helpers\NResponse;

if (! function_exists('human_name')) {
    function human_name($fullName, $format = 'lf')
    {
        return NName::of($fullName)->format($format);
    }
}

if (! function_exists('nRes')) {
    function nRes($data = null, $message = 'Success.', $code = 200)
    {
        $res = new NResponse;

        if (is_null($data)) {
            return $res;
        }

        return $res->response($data, $message, $code);
    }
}

if (! function_exists('phuongnam_path')) {
    function phuongnam_path()
    {
        return '';
    }
}

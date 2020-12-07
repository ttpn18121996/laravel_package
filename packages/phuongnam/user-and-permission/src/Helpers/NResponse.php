<?php

namespace PhuongNam\UserAndPermission\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class NResponse extends SymfonyResponse
{

    /**
     * Trả về json theo cấu trúc chuẩn.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array|stdClass  $data
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($data, $message = 'Success.', $code = 200)
    {
        if (is_object($data)) {
            if ($data instanceof Arrayable) {
                $data = $data->toArray();
            } else {
                $data = (array) $data;
            }
        }

        return response()->json([
            'status' => $code,
            'statusText' => static::$statusTexts[$code],
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Trả về kết quả khi chưa chứng thực.
     *
     * @param  string  $message
     * @param  array  $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function res401($message = 'Unauthorized.', array $data = [])
    {
        return $this->response($data, $message, 401);
    }

    /**
     * Trả về mảng theo cấu trúc chuẩn.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array|stdClass  $data
     * @param  string  $message
     * @param  int  $code
     * @return array
     */
    public function rawRes($data, $message = 'Success.', $code = 200)
    {
        return [
            'status' => $code,
            'statusText' => static::$statusTexts[$code],
            'message' => $message,
            'data' => $data
        ];
    }
}

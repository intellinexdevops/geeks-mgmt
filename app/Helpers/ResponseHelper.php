<?php

namespace App\Helpers;


class ResponseHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function success(
        $data,
        $message,
        $statusCode
    ) {
        return response()->json([
            'code' => 1,
            'msg' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function error(
        $message,
        $statusCode
    ) {
        return response()->json([
            'code' => 0,
            'msg' => $message,
            'data' => null
        ], $statusCode);
    }
}

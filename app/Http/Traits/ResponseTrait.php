<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * return error response.
     *
     * @param array|object $data
     * @param int $code
     * @param string|array|null $message
     * @return JsonResponse
     */
    public function sendResponse(array|object $data, int $code, string|array|null $message): JsonResponse
    {
        $response = [
            'status' => $code,
            'time'   => date('Y-m-d H:i:s'),
            'timeInMilliSecond' => 1000 * strtotime(date('Y-m-d H:i:s')),
            'msg'   => $message,
            'data'  => $data
        ];
        return response()->json($response, $code);
    }
}

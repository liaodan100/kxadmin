<?php

namespace KxAdmin\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

trait ApiResponse
{
    public function success(mixed $data, string $message = 'SUCCESS', int $code = 200): JsonResponse
    {
        return Response::json([
            'code' =>  $code,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function error(array $data, string $message = 'ERROR', int $code = 400, int $status = 400): JsonResponse
    {
        return Response::json([
            'code' =>  $code,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}

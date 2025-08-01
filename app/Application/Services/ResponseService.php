<?php

namespace App\Application\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    public function success(string $message = '', $data = null, int $status = 200, string $wrapper = 'data'): JsonResponse {
        return response()->json(
            [
                'error' => false,
                'message' => $message . " com sucesso.",
                $wrapper => $data
            ], $status
        );
    }

    public function error(string $message, int $status = 400): JsonResponse {
        return response()->json(
            [
                'error' => true,
                'message' => $message
            ], $status
        );
    }
}

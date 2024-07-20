<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

trait ApiResponse
{
    /**
     * sucess
     *
     * @param  array $data
     * @param  string|null $message
     * @param  int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, string $message = null, int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * error
     *
     * @param  string|array|null $message
     * @param  int $code
     * @param  array|string|null $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message = null, $data = null, Exception $exception = null)
    {
        if ($exception)
            Log::error("{$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}");

        if ($exception->getCode() === 422) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $message
            ], 422);
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data
        ], $exception->getCode() > 0 ? $exception->getCode() : 500);
    }
}

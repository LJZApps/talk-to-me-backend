<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseUtil
{
    /**
     * Generates an error response.
     *
     * @param string $error_code needed
     * @param string $error_message needed
     * @param int $statusCode default 400 (optional)
     * @param bool $success default false (optional)
     */
    public function successResponse(array $data = null): JsonResponse
    {
        if (!is_null($data)) {
            return new JsonResponse([
                "success" => true,
                "data" => $data
            ]);
        }

        return new JsonResponse([
            "success" => true
        ]);
    }

    public function errorResponse(string $error_code, string $error_message, string $exception_message = null, int $status = 400): JsonResponse
    {
        if (!is_null($exception_message)) {
            return new JsonResponse([
                "success" => false,
                "error_code" => $error_code,
                "error_message" => $error_message,
                "exception_message" => $exception_message
            ], $status);
        }

        return new JsonResponse([
            "success" => false,
            "error_code" => $error_code,
            "error_message" => $error_message
        ], $status);
    }

    public function internalErrorResponse(string $exception_message = null): JsonResponse
    {
        if (!is_null($exception_message)) {
            return new JsonResponse([
                "success" => false,
                "error_code" => "internal_error",
                "error_message" => "Something went wrong on our end.",
                "exception_message" => $exception_message
            ], 500);
        }

        return new JsonResponse([
            "success" => false,
            "error_code" => "internal_error",
            "error_message" => "Something went wrong on our end."
        ], 500);
    }
}

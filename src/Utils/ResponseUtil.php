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
    public function errorResponse(string $error_code, string $error_message, array $extra_data = null, int $statusCode = 400, bool $success = false): JsonResponse
    {
        if (isset($extra_data)) {
            $response = new JsonResponse(
                [
                    'success' => $success,
                    'error_code' => $error_code,
                    'error_message' => $error_message,
                    'extra_data' => $extra_data,
                ]
            );
        } else {
            $response = new JsonResponse(
                [
                    'success' => $success,
                    'error_code' => $error_code,
                    'error_message' => $error_message,
                ]
            );
        }

        $response->setStatusCode($statusCode);

        return $response;
    }

    public function arrayErrorResponse(string $error_code, string $error_message, array $more_context = null, int $statusCode = 400, bool $success = false): array
    {
        if (isset($more_context)) {
            $response = array(
                'success' => $success,
                'error_code' => $error_code,
                'error_message' => $error_message,
                'more_context' => $more_context,
            );
        } else {
            $response = array(
                'success' => $success,
                'error_code' => $error_code,
                'error_message' => $error_message,
            );
        }

        return $response;
    }
}

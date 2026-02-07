<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * API Response trait for standardized responses.
 * Per 07_api_specification.md §1.2
 */
trait ApiResponse
{
    /**
     * Success response.
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Operation successful',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error response.
     */
    protected function errorResponse(
        string $message,
        int $code = 400,
        array $errors = null,
        mixed $data = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response.
     */
    protected function validationErrorResponse(
        array $errors,
        string $message = 'Validation error'
    ): JsonResponse {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Not found response.
     */
    protected function notFoundResponse(
        string $message = 'Resource not found'
    ): JsonResponse {
        return $this->errorResponse($message, 404);
    }

    /**
     * Unauthorized response.
     */
    protected function unauthorizedResponse(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return $this->errorResponse($message, 401);
    }

    /**
     * Forbidden response.
     */
    protected function forbiddenResponse(
        string $message = 'Access denied'
    ): JsonResponse {
        return $this->errorResponse($message, 403);
    }
}

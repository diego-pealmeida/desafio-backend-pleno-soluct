<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    private function getDefaultResponseHeaders(): array
    {
        return [
            'Content-Type' => 'application/json'
        ];
    }

    public function successResponse(mixed $response, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json($response, $code, $this->getDefaultResponseHeaders());
    }

    public function noContentResponse(): Response
    {
        return response()->noContent();
    }

    public function errorResponse(string|array $errors, int $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        if (is_string($errors)) $errors = [$errors];

        return response()->json(['errors' => $errors], $code, $this->getDefaultResponseHeaders());
    }
}

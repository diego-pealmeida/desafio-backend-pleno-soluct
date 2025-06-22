<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

abstract class Controller
{
    use ApiResponse;

    public function internalErrorResponse(string $action): JsonResponse
    {
        return $this->errorResponse(
            "Não foi possível $action! Tente novamente ou entre em contato com a administrador do sistema.",
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}

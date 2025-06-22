<?php

namespace App\Http\Controllers;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(private Service $authService) {
        //
    }

    public function login(LoginRequest $request)
    {
        try {
            $accessToken = $this->authService->login($request->toData());
        } catch (InvalidCredentialsException) {
            return $this->errorResponse(
                'O e-mail e/ou senha fornecidos são inválidos!',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->internalErrorResponse('realizar o login');
        }

        return $this->successResponse($accessToken);
    }

    public function logout()
    {
        try {
            $this->authService->revokeToken();
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->internalErrorResponse('realizar o logout');
        }

        return $this->noContentResponse();
    }
}

<?php

namespace App\Http\Controllers;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AccessTokenResource;
use App\Services\Auth\Service;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

#[Group(name: 'Autenticação')]
class AuthController extends Controller
{
    public function __construct(private Service $authService) {
        //
    }

    /**
     * Autenticar
     *
     * @unauthenticated
     */
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

        return $this->successResponse(new AccessTokenResource($accessToken));
    }

    /**
     * Revogar Token Atual
     *
     *  @unauthenticated
     */
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

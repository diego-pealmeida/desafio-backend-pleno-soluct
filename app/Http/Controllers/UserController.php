<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateRequest;
use App\Repositories\User\Repository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(private Repository $repository) {
        //
    }

    public function store(CreateRequest $request)
    {
        try {
            $user = $this->repository->create($request->toData());
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->internalErrorResponse('cadastrar o usuário');
        }

        return $this->successResponse($user, Response::HTTP_CREATED);
    }
}

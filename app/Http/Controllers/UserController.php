<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateRequest;
use App\Http\Resources\User\Resource;
use App\Repositories\User\Repository;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

#[Group(name: 'Usuários')]
class UserController extends Controller
{
    public function __construct(private Repository $repository) {
        //
    }

    /**
     * Registrar
     *
     * @unauthenticated
     */
    public function store(CreateRequest $request)
    {
        try {
            $user = $this->repository->create($request->toData());
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->internalErrorResponse('cadastrar o usuário');
        }

        return $this->successResponse(new Resource($user), Response::HTTP_CREATED);
    }
}

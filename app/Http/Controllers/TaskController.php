<?php

namespace App\Http\Controllers;

use App\Exceptions\Task\NotFoundException;
use App\Http\Requests\Task\CreateRequest;
use App\Http\Requests\Task\ListRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\Task\ListResource;
use App\Http\Resources\Task\Resource;
use App\Services\Tasks\Service;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

#[Group(name: 'Tarefas')]
class TaskController extends Controller
{
    public function __construct(private Service $taskService) {
        //
    }

    private function taskNotFoundResponse(): JsonResponse
    {
        return $this->errorResponse(
            "A tarefa informada é inválida",
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Listar
     */
    public function index(ListRequest $request)
    {
        $list = $this->taskService->listTasks($request->toData(), $request->toPaginationData(), $request->toOrdernationData());

        return $this->successResponse(new ListResource($list));
    }

    /**
     * Incluir
     */
    public function store(CreateRequest $request)
    {
        try {
            $task = $this->taskService->createTask($request->toData());
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->internalErrorResponse('cadastrar a tarefa');
        }

        return $this->successResponse(new Resource($task), Response::HTTP_CREATED);
    }

    /**
     * Buscar Específica
     */
    public function show(int $taskId)
    {
        try {
            $task = $this->taskService->getTask($taskId);
        } catch (NotFoundException $e) {
            return $this->taskNotFoundResponse();
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->internalErrorResponse('cadastrar a tarefa');
        }

        return $this->successResponse(new Resource($task));
    }

    /**
     * Atualizar
     */
    public function update(UpdateRequest $request, int $taskId)
    {
        try {
            $task = $this->taskService->updateTask($taskId, $request->toData());
        } catch (NotFoundException $e) {
            return $this->taskNotFoundResponse();
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->internalErrorResponse('atualizar a tarefa');
        }

        return $this->successResponse(new Resource($task));
    }

    /**
     * Remover
     */
    public function destroy(int $taskId)
    {
        try {
            $this->taskService->deleteTask($taskId);
        } catch (NotFoundException $e) {
            return $this->taskNotFoundResponse();
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->internalErrorResponse('remover a tarefa');
        }

        return $this->noContentResponse();
    }
}

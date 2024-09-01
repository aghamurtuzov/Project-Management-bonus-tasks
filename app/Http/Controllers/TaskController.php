<?php

namespace App\Http\Controllers;

use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Http\Requests\TaskRequest;
use App\Repository\Task\Service\TaskService;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponse;

    protected $task;

    public function __construct(TaskService $task)
    {
        $this->task = $task;
        $this->middleware('permission:task-list', ['only' => ['index']]);
        $this->middleware('permission:task-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:task-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:task-show', ['only' => ['show']]);
        $this->middleware('permission:task-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = $this->task->index();

        return $this->sendResponse($tasks, 'Task data successfully retrieved.', 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        try {
            $request->validated();

            $task = $this->task->store($request->all());

            event(new TaskCreated($task));

            return $this->sendResponse($task, 'Task data successfully created.', 201);
        } catch (\Exception $e) {
            return $this->sendError($e, "Failed to create project.", 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = $this->task->show($id);

        return $this->sendResponse($task, 'Task data successfully retrieved.', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, string $id)
    {
        try {
            $request->validated();
            $task =  $this->task->update($request->all(), $id);

            event(new TaskUpdated($task));
            return $this->sendResponse([], 'Task data successfully updated.', 200);
        } catch (\Exception $e) {
            return $this->sendError($e, 'Failed to update project.', 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
           $task = $this->task->destroy($id);
            event(new TaskDeleted($task));
            return $this->sendResponse([], 'Task data successfully deleted.', 204);
        } catch (\Exception $e) {
            return $this->sendError($e, 'Failed to delete project.', 400);
        }
    }

    public function search(Request $request)
    {
        $name = $request->get('name');
        $status = $request->get('status');
        $projectId = $request->get('project_id');

        $tasks = $this->task->search($projectId, $name, $status);

        return $this->sendResponse($tasks, 'Task search results successfully retrieved.', 200);
    }
}

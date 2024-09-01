<?php

namespace App\Http\Controllers;

use App\Events\ProjectCreated;
use App\Events\ProjectDeleted;
use App\Events\ProjectUpdated;
use App\Http\Requests\ProjectRequest;
use App\Repository\Project\Service\ProjectService;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    protected $project;

    public function __construct(ProjectService $project)
    {
        $this->project = $project;
        $this->middleware('permission:project-list', ['only' => ['index']]);
        $this->middleware('permission:project-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:project-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:project-show', ['only' => ['show']]);
        $this->middleware('permission:project-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = $this->project->index();

        return $this->sendResponse($projects, 'Project data successfully retrieved.', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        try {
            $request->validated();

            $project = $this->project->store($request->all());

            event(new ProjectCreated($project));

            return $this->sendResponse($project, 'Project data successfully created.', 201);
        } catch (\Exception $e) {
            return $this->sendError($e, "Failed to create project.", 400);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = $this->project->show($id);

        return $this->sendResponse($project, 'Project data successfully retrieved.', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, string $id)
    {
        try {
            $request->validated();

           $project =  $this->project->update($request->all(), $id);

            event(new ProjectUpdated($project));

            return $this->sendResponse([], 'Project data successfully updated.', 200);
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
            $project = $this->project->destroy($id);

            event(new ProjectDeleted($project));

            return $this->sendResponse([], 'Project data successfully deleted.', 204);
        } catch (\Exception $e) {
            return $this->sendError($e, 'Failed to delete project.', 400);
        }

    }

    public function search(Request $request)
    {
        $name = $request->get('name');

        $projects = $this->project->search($name);

        return $this->sendResponse($projects, 'Project search results successfully retrieved.', 200);
    }
}

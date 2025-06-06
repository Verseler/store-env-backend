<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreProjectRequest;
use App\Http\Requests\V1\UpdateProjectRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $cachedProjects = Cache::flexible('projects', [9, 10], function () {
            return Project::with('envs')
                ->where('user_id', Auth::id())
                ->paginate(10);
        });

        return response()->json(
            $cachedProjects
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        return response()->json(Project::create(
            $request->validated()
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): JsonResponse
    {

        Gate::authorize('view', [$project]);

        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('destroy', [$project]);

        $project->delete();

        return response()->json(['message' => 'The project was deleted.']);
    }
}

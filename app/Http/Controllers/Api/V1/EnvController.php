<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Env;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreEnvRequest;
use App\Http\Requests\V1\UpdateEnvRequest;
use Illuminate\Http\JsonResponse;

class EnvController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEnvRequest $request): JsonResponse
    {
        return response()->json(
            Env::create(
                $request->validated()
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnvRequest $request, Env $env): JsonResponse
    {
        $env->update($request->validated());

        return response()->json($env);
    }
}

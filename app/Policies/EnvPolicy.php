<?php

namespace App\Policies;

use App\Models\Env;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EnvPolicy
{

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Env $env): bool
    {
        return $user && $user->id == $env->project_id;
    }

    /**
     * Determine whether the user can delete the project.
     */
    public function delete(User $user, Env $env): bool
    {
        return false;
    }
}

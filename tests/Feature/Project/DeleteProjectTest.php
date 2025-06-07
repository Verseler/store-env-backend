<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('allows a user to delete their own project', function () {
    $project = Project::factory()->for($this->user)->create();

    $response = $this->actingAs($this->user)->deleteJson("/api/v1/projects/{$project->id}");
    Log::info('delete a project of a user ', ['response' => $response]);
    $response->assertOk();
    $response->assertJson([
        'message' => 'The project was deleted.',
    ]);

    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});


it('prevents unauthenticated users from deleting a project', function () {
    $project = Project::factory()->create();

    $response = $this->deleteJson("/api/v1/projects/{$project->id}");

    $response->assertUnauthorized();
    $this->assertDatabaseHas('projects', ['id' => $project->id]);
});

it('prevents users from deleting projects they do not own', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();
    $project = Project::factory()->for($owner)->create();

    $response = $this->actingAs($attacker)->deleteJson("/api/v1/projects/{$project->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('projects', ['id' => $project->id]);
});

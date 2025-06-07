<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = User::factory()->create();
});

//* Test for getting projects (Project::class, index)
it('prevents unauthenticated access to project listing', function () {
    $response = $this->getJson('/api/v1/projects');
    $response->assertUnauthorized();
});

it('returns empty list if the authenticated user has no projects', function () {
    Cache::shouldReceive('flexible')
        ->once()
        ->andReturn(Project::with('envs')
            ->where('user_id', $this->user->id)
            ->paginate(10));

    $response = $this->actingAs($this->user)->getJson('/api/v1/projects');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

it('paginates projects correctly', function () {
    Project::factory()
        ->count(15)
        ->for($this->user)
        ->create();

    Cache::shouldReceive('flexible')
        ->once()
        ->andReturn(Project::with('envs')
            ->where('user_id', $this->user->id)
            ->paginate(10));

    $response = $this->actingAs($this->user)->getJson('/api/v1/projects?page=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(10);
});


//* Test for getting a project (Project::class, show)
it('allows a user to view their own project', function () {
    $project = Project::factory()->for($this->user)->create();

    $response = $this->actingAs($this->user)->getJson("/api/v1/projects/{$project->id}");

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $project->id,
        'title' => $project->title,
        'description' => $project->description,
        'user_id' => $this->user->id,
    ]);
});

it('prevents unauthenticated users from viewing a project', function () {
    $project = Project::factory()->create();

    $response = $this->getJson("/api/v1/projects/{$project->id}");

    $response->assertUnauthorized(); // 401
});

it('prevents a user from viewing a project they do not own', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();

    $project = Project::factory()->for($owner)->create();

    $response = $this->actingAs($attacker)->getJson("/api/v1/projects/{$project->id}");

    $response->assertForbidden(); // 403 from Gate
});

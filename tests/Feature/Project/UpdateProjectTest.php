<?php
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('allows a user to update their own project', function () {
    $project = Project::factory()->for($this->user)->create();

    $payload = [
        'title' => 'Updated Project Title',
        'description' => 'Updated description',
    ];

    $response = $this->actingAs($this->user)->putJson("/api/v1/projects/{$project->id}", $payload);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $project->id,
        'title' => $payload['title'],
        'description' => $payload['description'],
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'title' => $payload['title'],
        'description' => $payload['description'],
    ]);
});

it('prevents unauthenticated users from updating a project', function () {
    $project = Project::factory()->create();

    $response = $this->putJson("/api/v1/projects/{$project->id}", [
        'title' => 'Hacked title',
        'description' => 'Hacked desc',
    ]);

    $response->assertUnauthorized();
});

it('prevents users from updating a project they do not own', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $project = Project::factory()->for($owner)->create();

    $payload = [
        'title' => 'Intrusion',
        'description' => 'Trying to hijack',
    ];

    $response = $this->actingAs($intruder)->putJson("/api/v1/projects/{$project->id}", $payload);

    $response->assertForbidden();

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
        'title' => $payload['title'],
    ]);
});

it('validates update input', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    $payload = [
        'title' => '', // invalid
        'description' => 12345, // not a string
    ];

    $response = $this->actingAs($user)->putJson("/api/v1/projects/{$project->id}", $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title', 'description']);
});

it('ignores attempt to change user_id on update', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    $otherUser = User::factory()->create();

    $payload = [
        'title' => 'Attempted Takeover',
        'description' => 'Should succeed',
        'user_id' => $otherUser->id, // trying to hijack ownership
    ];

    $response = $this->actingAs($user)->putJson("/api/v1/projects/{$project->id}", $payload);

    $response->assertOk();
    $response->assertJsonMissing(['user_id' => $otherUser->id]);
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'user_id' => $user->id,
    ]);
});

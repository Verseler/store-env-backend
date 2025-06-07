<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('allows an authenticated user to store a project', function () {
    $payload = [
        'title' => 'Project 1',
        'description' => 'Test 1 project ...',
    ];

    $response = $this->actingAs($this->user)->postJson('/api/v1/projects', $payload);

    $response->assertCreated(); // it means the status is 201 (created)
    $response->assertJsonFragment([
        'title' => $payload['title'],
        'description' => $payload['description'],
        'user_id' => $this->user->id,
    ]);
    $this->assertDatabaseHas('projects', $payload);
});

it('prevents an unauthenticated user from storing a project', function () {
    $payload = [
        'title' => 'Unauthorized Project',
        'description' => 'This should fail.',
        'user_id' => 1, // arbitrary
    ];

    $response = $this->postJson('/api/v1/projects', $payload);

    $response->assertUnauthorized();
});

it('validates required fields when storing a project', function () {
    $payload = [
        'title' => '', // missing title
        // 'description' is intentionally omitted because it's nullable
    ];

    $response = login()->postJson('/api/v1/projects', $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
    $response->assertJsonMissingValidationErrors(['description']);
});

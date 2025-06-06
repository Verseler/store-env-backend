<?php

use App\Models\User;

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/v1/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    expect($response->json('token'))
        ->toBeString()
        ->toBeGreaterThan(1);

    expect($response->json('user'))
        ->not()
        ->toBeNull();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/api/v1/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    //use the authenticated user token to logout because the token is required
    $response = $this->actingAs($user)->post('/api/v1/logout');

    $this->assertAuthenticated();

    expect($response->json('message'))
        ->toBe("You are logged out.");
});

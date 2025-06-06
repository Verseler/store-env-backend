<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can authenticate a user using the login screen', function () {
    $response = $this->post('/api/v1/login', [
        'email' => $this->user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $this->assertAuthenticated();
    expect($response->json('token'))
        ->toBeTruthy()
        ->toBeString();
    expect($response->json('user'))
        ->toBeTruthy();
});

it('can not authenticate a user with invalid password', function () {
    $this->post('/api/v1/login', [
        'email' => $this->user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

it('can logout a user', function () {
    //use the authenticated user token to logout because the token is required
    $response = login()->post('/api/v1/logout');

    $this->assertAuthenticated();

    expect($response->json('message'))
        ->toBe("You are logged out.");
});

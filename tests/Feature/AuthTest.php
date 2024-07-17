<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a user to register', function () {
    $response = $this->postJson('/api/user/register', [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);

    expect($response->json('success'))->toBeTrue();
});

it('allows a user to login', function () {
    User::factory()->create([
        'email' => 'johndoe@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/user/login', [
        'email' => 'johndoe@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    
    expect($response->json('success'))->toBeTrue();
    expect($response->json('data'))->toHaveKey('token');
});

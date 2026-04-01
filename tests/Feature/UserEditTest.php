<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('edit page is accessible for an existing user', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create();
    Auth::login($admin);

    $response = $this->get(route('users.edit', $user));

    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertSee($user->email);
});

test('user information can be updated', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com'
    ]);
    $admin = User::factory()->create();
    Auth::login($admin);

    $response = $this->patch(route('users.update', $user), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'telegram_chat_id' => '123456789',
        'whatsapp_number' => '+58 412 123 4567'
    ]);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'User updated successfully!');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'telegram_chat_id' => '123456789',
        'whatsapp_number' => '584121234567' // Cleaned number
    ]);
});

test('update validation ensures uniqueness except for current user', function () {
    $otherUser = User::factory()->create(['email' => 'other@example.com']);
    $user = User::factory()->create(['email' => 'user@example.com']);
    $admin = User::factory()->create();
    Auth::login($admin);

    // Try to update user with otherUser's email
    $response = $this->patch(route('users.update', $user), [
        'name' => 'Some Name',
        'email' => 'other@example.com'
    ]);

    $response->assertSessionHasErrors(['email']);
    
    // Try to update user with their OWN email (should pass)
    $response = $this->patch(route('users.update', $user), [
        'name' => 'New Name',
        'email' => 'user@example.com'
    ]);

    $response->assertSessionHasNoErrors();
});

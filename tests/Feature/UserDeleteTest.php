<?php

use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('a user can be deleted along with their message history', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create();
    Auth::login($admin);

    // Create some messages sent by and received by the user
    Message::factory()->create(['user_id' => $user->id, 'recipient_id' => $admin->id]);
    Message::factory()->create(['user_id' => $admin->id, 'recipient_id' => $user->id]);

    $response = $this->delete(route('users.destroy', $user));

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', 'User deleted successfully!');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    
    // Verify messages are also deleted
    $this->assertDatabaseMissing('messages', ['user_id' => $user->id]);
    $this->assertDatabaseMissing('messages', ['recipient_id' => $user->id]);
});

test('unauthenticated users cannot delete users', function () {
    $user = User::factory()->create();

    $response = $this->delete(route('users.destroy', $user));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

<?php

use App\Models\User;
use App\Models\Message;
use App\Services\EmailService;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicMessage;
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('EmailService sends an email and records it in the database', function () {
    Mail::fake();
    
    $user = User::factory()->create([
        'email' => 'test@example.com'
    ]);
    
    Auth::login($user); // Login for message record user_id

    $service = new EmailService();
    $service->sendMessage($user, 'Test Content');

    // Verify email was sent
    Mail::assertSent(DynamicMessage::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->content === 'Test Content';
    });

    // Verify database record
    $this->assertDatabaseHas('messages', [
        'platform'     => 'email',
        'message'      => 'Test Content',
        'status'       => 'sent',
        'recipient_id' => $user->id,
    ]);
});

test('EmailService handles multiple users (mass message)', function () {
    Mail::fake();
    
    $users = User::factory()->count(3)->create();
    $usersArray = $users->all();

    $sender = User::factory()->create();
    Auth::login($sender);

    $service = new EmailService();
    $service->sendMassMessage($usersArray, 'Mass Content');

    // Verify 3 emails were sent
    Mail::assertSent(DynamicMessage::class, 3);
    
    // Verify 3 database records
    $this->assertEquals(3, Message::where('platform', 'email')->where('message', 'Mass Content')->count());
});

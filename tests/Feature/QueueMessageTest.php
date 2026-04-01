<?php

use App\Models\User;
use App\Models\Message;
use App\Jobs\ProcessMessage;
use Illuminate\Support\Facades\Queue;
use App\Services\EmailService;
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('mass sending dispatches background jobs', function () {
    Queue::fake();
    
    $users = User::factory()->count(3)->create();
    $usersArray = $users->all();
    
    $admin = User::factory()->create();
    Auth::login($admin);

    $service = new EmailService();
    $service->sendMassMessage($usersArray, 'Test Queue Content');

    // Verify 3 jobs were dispatched
    Queue::assertPushed(ProcessMessage::class, 3);
});

test('ProcessMessage job executes the service sendMessage method', function () {
    // Para probar la ejecución real del Job, no usamos Queue::fake()
    $user = User::factory()->create(['email' => 'jobtest@example.com']);
    $admin = User::factory()->create();
    
    // El Job hará Auth::loginUsingId($admin->id)
    
    $job = new ProcessMessage('email', $user, 'Job Content', $admin->id);
    
    // Usamos Mail::fake() porque EmailService llama a Mail
    \Illuminate\Support\Facades\Mail::fake();
    
    $job->handle();

    // Verificamos que se creó el registro en la BD (lo cual hace EmailService::sendMessage)
    $this->assertDatabaseHas('messages', [
        'platform' => 'email',
        'message' => 'Job Content',
        'recipient_id' => $user->id,
        'status' => 'sent'
    ]);
});

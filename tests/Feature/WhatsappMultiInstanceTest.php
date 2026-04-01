<?php

use App\Models\User;
use App\Models\WhatsappInstance;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessMessage;
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('a user can create a whatsapp instance', function () {
    $user = User::factory()->create();
    Auth::login($user);

    // Mock the HTTP call to Evolution API
    \Illuminate\Support\Facades\Http::fake([
        '*/instance/create' => \Illuminate\Support\Facades\Http::response(['status' => 'SUCCESS'], 200),
    ]);

    $response = $this->post(route('whatsapp.store'), [
        'name' => 'TestInstance',
    ]);

    $response->assertRedirect(route('whatsapp.index'));
    $this->assertDatabaseHas('whatsapp_instances', [
        'name' => 'TestInstance',
        'user_id' => $user->id,
    ]);
});

test('sending whatsapp messages with a specific instance dispatches jobs with correct metadata', function () {
    Queue::fake();
    
    $user = User::factory()->create();
    Auth::login($user);

    $instance = WhatsappInstance::create([
        'name' => 'MyPhone',
        'instance_id' => 'MyPhone',
        'user_id' => $user->id,
        'status' => 'connected',
    ]);

    $recipient = User::factory()->create(['whatsapp_number' => '584121234567']);

    $response = $this->post(route('send'), [
        'platform' => 'whatsapp',
        'users' => [$recipient->id],
        'message' => 'Hello from instance',
        'whatsapp_instance_id' => $instance->id,
    ]);

    $response->assertSessionHas('success');

    // Verify job was pushed with the instance ID in options
    Queue::assertPushed(ProcessMessage::class, function ($job) use ($instance) {
        $reflection = new \ReflectionClass($job);
        $options = $reflection->getProperty('options');
        $options->setAccessible(true);
        $optionsValue = $options->getValue($job);
        
        return $optionsValue['whatsapp_instance_id'] == $instance->id;
    });
});

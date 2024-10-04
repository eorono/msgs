<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;

class DiscordService implements SendsMessages
{
    public function sendMessage(User $user, $message)
    {
        \Log::info("[DISCORD]: Message sent to user: $user->email - \"$message\"");
        Message::create([
            'platform' => 'discord',
            'message' => $message,
            'user_id' => auth()->id(),
            'status' => 'sent',
            'recipient_id' => $user->id,
        ]);
    }

    public function sendMassMessage(array $users, $message)
    {
        foreach ($users as $user) {
            $this->sendMessage($user, $message);
        }
    }
}

<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;

class TelegramService implements SendsMessages
{
    public function sendMessage(User $user, $message)
    {
        $status = 'failed';

        if ($user->telegram_chat_id) {
            $token = env('TELEGRAM_BOT_TOKEN');
            $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $user->telegram_chat_id,
                'text' => $message,
            ]);

            if ($response->successful()) {
                $status = 'sent';
            } else {
                \Log::error("[TELEGRAM ERROR]: " . $response->body());
            }
        } else {
            \Log::warning("[TELEGRAM WARNING]: User {$user->email} does not have a telegram_chat_id.");
        }

        Message::create([
            'platform' => 'telegram',
            'message' => $message,
            'user_id' => auth()->id(),
            'status' => $status,
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

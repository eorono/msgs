<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;

class WhatsappService implements SendsMessages
{
    public function sendMessage(User $user, $message)
    {
        $status = 'failed';

        if ($user->whatsapp_number) {
            $apiUrl = env('EVOLUTION_API_URL');
            $instance = env('EVOLUTION_API_INSTANCE');
            $apiKey = env('EVOLUTION_API_KEY');

            if ($apiUrl && $instance && $apiKey) {
                // El número ya se limpia al guardar, pero por si acaso nos aseguramos de que no haya +, -, o espacios
                $cleanNumber = preg_replace('/[^0-9]/', '', $user->whatsapp_number);

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("{$apiUrl}/message/sendText/{$instance}", [
                    'number' => $cleanNumber,
                    'text' => $message,
                ]);

                if ($response->successful()) {
                    $status = 'sent';
                } else {
                    \Log::error("[WHATSAPP ERROR]: " . $response->body());
                }
            } else {
                \Log::error("[WHATSAPP ERROR]: Evolution API environment variables are not configured.");
            }
        } else {
            \Log::warning("[WHATSAPP WARNING]: User {$user->email} does not have a whatsapp_number.");
        }

        Message::create([
            'platform' => 'whatsapp',
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

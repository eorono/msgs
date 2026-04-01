<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessMessage;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio de mensajería para la plataforma Telegram.
 *
 * Realiza llamadas HTTP reales a la API de Telegram para enviar mensajes.
 * Requiere la variable de entorno TELEGRAM_BOT_TOKEN configurada.
 * El usuario destinatario debe tener un telegram_chat_id registrado.
 *
 * @implements SendsMessages
 */
class TelegramService implements SendsMessages
{
    /**
     * Envía un mensaje a un usuario a través de Telegram.
     *
     * Realiza una petición POST a la API de Telegram (https://api.telegram.org/bot{token}/sendMessage)
     * con el chat_id del destinatario y el contenido del mensaje.
     * Si el usuario no tiene telegram_chat_id, registra una advertencia.
     * Si la API retorna error, registra el error en los logs.
     *
     * @param  User    $user    Usuario destinatario (debe tener telegram_chat_id)
     * @param  string  $message Contenido del mensaje
     * @return void
     */
    public function sendMessage(User $user, $message, array $options = [])
    {
        $status = 'failed';

        if ($user->telegram_chat_id) {
            $token = env('TELEGRAM_BOT_TOKEN');
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
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

    /**
     * Envía un mensaje masivo a múltiples usuarios a través de Telegram.
     *
     * Itera sobre la lista de usuarios y envía el mensaje a cada uno
     * individualmente, registrando el resultado de cada envío.
     *
     * @param  array   $users   Lista de usuarios destinatarios
     * @param  string  $message Contenido del mensaje
     * @return void
     */
    public function sendMassMessage(array $users, $message, array $options = [])
    {
        $senderId = Auth::id() ?? 1;

        foreach ($users as $user) {
            ProcessMessage::dispatch('telegram', $user, $message, $senderId, $options);
        }
    }
}

<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Jobs\ProcessMessage;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio de mensajería para la plataforma Slack.
 *
 * Actualmente simula el envío de mensajes registrándolos en los logs
 * de Laravel y guardándolos en la base de datos con estado "sent".
 * No realiza llamadas HTTP reales a la API de Slack.
 *
 * @implements SendsMessages
 */
class SlackService implements SendsMessages
{
    /**
     * Envía un mensaje a un usuario a través de Slack.
     *
     * Registra el mensaje en los logs y lo crea en la base de datos
     * con la plataforma "slack" y estado "sent".
     *
     * @param  User    $user    Usuario destinatario
     * @param  string  $message Contenido del mensaje
     * @return void
     */
    public function sendMessage(User $user, $message)
    {
        \Log::info("[SLACK]: Message sent to user: $user->email - \"$message\"");
        Message::create([
            'platform' => 'slack',
            'message' => $message,
            'user_id' => auth()->id(),
            'status' => 'sent',
            'recipient_id' => $user->id,
        ]);
    }

    /**
     * Envía un mensaje masivo a múltiples usuarios a través de Slack.
     *
     * Itera sobre la lista de usuarios y envía el mensaje a cada uno
     * individualmente.
     *
     * @param  array   $users   Lista de usuarios destinatarios
     * @param  string  $message Contenido del mensaje
     * @return void
     */
    public function sendMassMessage(array $users, $message)
    {
        $senderId = Auth::id() ?? 1;

        foreach ($users as $user) {
            ProcessMessage::dispatch('slack', $user, $message, $senderId);
        }
    }
}

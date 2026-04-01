<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;

/**
 * Servicio de mensajería para la plataforma Discord.
 *
 * Actualmente simula el envío de mensajes registrándolos en los logs
 * de Laravel y guardándolos en la base de datos con estado "sent".
 * No realiza llamadas HTTP reales a la API de Discord.
 *
 * @implements SendsMessages
 */
class DiscordService implements SendsMessages
{
    /**
     * Envía un mensaje a un usuario a través de Discord.
     *
     * Registra el mensaje en los logs y lo crea en la base de datos
     * con la plataforma "discord" y estado "sent".
     *
     * @param  User    $user    Usuario destinatario
     * @param  string  $message Contenido del mensaje
     * @return void
     */
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

    /**
     * Envía un mensaje masivo a múltiples usuarios a través de Discord.
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
        foreach ($users as $user) {
            $this->sendMessage($user, $message);
        }
    }
}

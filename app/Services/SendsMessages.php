<?php

namespace App\Services;

use App\Models\User;

/**
 * Interfaz que define el contrato para los servicios de mensajería.
 *
 * Todos los servicios de plataforma (Discord, Slack, Telegram, WhatsApp)
 * deben implementar esta interfaz para garantizar un comportamiento consistente
 * al enviar mensajes individuales o masivos.
 */
interface SendsMessages
{
    /**
     * Envía un mensaje a un usuario específico.
     *
     * @param  User    $user     Usuario destinatario del mensaje
     * @param  string  $message  Contenido del mensaje a enviar
     * @param  array   $options  Opciones adicionales (ej: whatsapp_instance_id)
     * @return void
     */
    public function sendMessage(User $user, $message, array $options = []);

    /**
     * Envía un mensaje masivo a múltiples usuarios.
     *
     * @param  array   $users    Lista de usuarios destinatarios
     * @param  string  $message  Contenido del mensaje a enviar
     * @param  array   $options  Opciones adicionales
     * @return void
     */
    public function sendMassMessage(array $users, $message, array $options = []);
}

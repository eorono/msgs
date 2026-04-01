<?php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use App\Mail\DynamicMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessMessage;

/**
 * Servicio para el envío de mensajes a través de correo electrónico.
 *
 * Implementa la interfaz SendsMessages y utiliza el sistema de correo nativo de Laravel.
 */
class EmailService implements SendsMessages
{
    /**
     * Envía un correo electrónico a un usuario.
     *
     * @param  User    $user    Usuario destinatario
     * @param  string  $message Contenido del mensaje
     * @return void
     */
    public function sendMessage(User $user, $message, array $options = [])
    {
        try {
            // Envío real del correo
            Mail::to($user->email)->send(new DynamicMessage($message));

            // Registro en base de datos
            Message::create([
                'platform'     => 'email',
                'message'      => $message,
                'status'       => 'sent',
                'user_id'      => Auth::id() ?? 1, // Fallback al admin si es desde CLI
                'recipient_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            // Registro de error en base de datos
            Message::create([
                'platform'     => 'email',
                'message'      => $message,
                'status'       => 'failed',
                'user_id'      => Auth::id() ?? 1,
                'recipient_id' => $user->id,
            ]);
            
            // Re-lanzar o loguear según política (aquí solo seguimos el patrón existente)
            \Log::error("Error enviando email a {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Envía un correo electrónico masivo a múltiples usuarios.
     *
     * @param  array   $users   Lista de usuarios
     * @param  string  $message Contenido del mensaje
     * @return void
     */
    public function sendMassMessage(array $users, $message, array $options = [])
    {
        $senderId = Auth::id() ?? 1;

        foreach ($users as $user) {
            ProcessMessage::dispatch('email', $user, $message, $senderId, $options);
        }
    }
}

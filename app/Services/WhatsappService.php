<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessMessage;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio de mensajería para la plataforma WhatsApp.
 *
 * Realiza llamadas HTTP reales a la Evolution API para enviar mensajes de WhatsApp.
 * Requiere las variables de entorno configuradas:
 * - EVOLUTION_API_URL: URL base de la Evolution API
 * - EVOLUTION_API_INSTANCE: Nombre de la instancia de WhatsApp
 * - EVOLUTION_API_KEY: Clave de autenticación para la API
 *
 * El usuario destinatario debe tener un whatsapp_number registrado.
 *
 * @implements SendsMessages
 */
class WhatsappService implements SendsMessages
{
    /**
     * Envía un mensaje a un usuario a través de WhatsApp.
     *
     * Realiza una petición POST a la Evolution API con el número de teléfono
     * limpio (solo dígitos) y el contenido del mensaje.
     * Si el usuario no tiene whatsapp_number, registra una advertencia.
     * Si las variables de entorno no están configuradas, registra un error.
     * Si la API retorna error, registra el error en los logs.
     *
     * @param  User    $user    Usuario destinatario (debe tener whatsapp_number)
     * @param  string  $message Contenido del mensaje
     * @return void
     */
    public function sendMessage(User $user, $message, array $options = [])
    {
        $status = 'failed';

        $instanceId = $options['whatsapp_instance_id'] ?? null;
        $instance = $instanceId ? \App\Models\WhatsappInstance::find($instanceId) : null;
        
        if ($user->whatsapp_number) {
            $apiUrl = env('EVOLUTION_API_URL');
            $instanceName = $instance ? $instance->name : env('EVOLUTION_API_INSTANCE');
            $apiKey = env('EVOLUTION_API_KEY');

            if ($apiUrl && $instanceName && $apiKey) {
                // El número ya se limpia al guardar, pero por si acaso nos aseguramos de que no haya +, -, o espacios
                $cleanNumber = preg_replace('/[^0-9]/', '', $user->whatsapp_number);

                $response = Http::withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("{$apiUrl}/message/sendText/{$instanceName}", [
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
            'whatsapp_instance_id' => $instanceId,
        ]);
    }

    /**
     * Envía un mensaje masivo a múltiples usuarios a través de WhatsApp.
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
            ProcessMessage::dispatch('whatsapp', $user, $message, $senderId, $options);
        }
    }
}

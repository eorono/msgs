<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

/**
 * Trabajo para procesar el envío de un mensaje en segundo plano.
 *
 * Se encarga de resolver el servicio de mensajería correspondiente
 * y ejecutar el envío para un usuario específico.
 */
class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Plataforma de envío (discord, slack, telegram, whatsapp, email).
     *
     * @var string
     */
    protected $platform;

    /**
     * Usuario destinatario.
     *
     * @var User
     */
    protected $user;

    /**
     * Contenido del mensaje.
     *
     * @var string
     */
    protected $message;

    /**
     * ID del usuario remitente (para auditoría).
     *
     * @var int
     */
    protected $senderId;

    /**
     * Crea una nueva instancia del trabajo.
     *
     * @param string $platform
     * @param User   $user
     * @param string $message
     * @param int    $senderId
     */
    public function __construct(string $platform, User $user, string $message, int $senderId)
    {
        $this->platform = $platform;
        $this->user = $user;
        $this->message = $message;
        $this->senderId = $senderId;
    }

    /**
     * Ejecuta el envío del mensaje resolviendo el servicio desde el contenedor.
     *
     * @return void
     */
    public function handle(): void
    {
        // Autenticamos al remitente para que las relaciones en el modelo Message
        // se creen con el user_id correcto.
        Auth::loginUsingId($this->senderId);

        // Resolvemos la clase del servicio desde la configuración
        $serviceClass = config("platforms.{$this->platform}");
        
        if ($serviceClass) {
            $service = app($serviceClass);
            $service->sendMessage($this->user, $this->message);
        }
    }
}

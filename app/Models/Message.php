<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Mensaje del sistema.
 *
 * Representa un mensaje enviado a través de una plataforma de mensajería
 * (Discord, Slack, Telegram, WhatsApp). Almacena el contenido del mensaje,
 * la plataforma utilizada, el estado del envío y las relaciones con el
 * usuario remitente y el destinatario.
 *
 * @property int         $id
 * @property string      $platform    Plataforma de envío (discord, slack, telegram, whatsapp)
 * @property string      $status      Estado del mensaje (sent, failed)
 * @property string      $message     Contenido del mensaje
 * @property int         $user_id     ID del usuario remitente
 * @property int         $recipient_id ID del usuario destinatario
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory;

    /**
     * Atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'platform',
        'status',
        'message',
        'user_id',
        'recipient_id',
    ];

    /**
     * Obtiene el usuario remitente del mensaje.
     *
     * Relación muchos-a-uno con el modelo User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el usuario destinatario del mensaje.
     *
     * Relación muchos-a-uno con el modelo User, usando la clave foránea recipient_id.
     *
     * @return BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}

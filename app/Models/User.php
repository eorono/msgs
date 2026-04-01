<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo de Usuario del sistema.
 *
 * Representa a los usuarios registrados en la aplicación de mensajería masiva.
 * Cada usuario puede tener múltiples mensajes enviados y puede estar vinculado
 * a plataformas externas como Telegram (telegram_chat_id) y WhatsApp (whatsapp_number).
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string|null $email_verified_at
 * @property string      $password
 * @property string|null $telegram_chat_id  ID del chat de Telegram del usuario
 * @property string|null $whatsapp_number   Número de teléfono de WhatsApp del usuario
 * @property string|null $remember_token
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_chat_id',
        'whatsapp_number',
    ];

    /**
     * Atributos ocultos para la serialización JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'recipient_id',
        'whatsapp_instance_id',
    ];

    /**
     * Obtiene los atributos que deben ser convertidos a tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Obtiene los mensajes enviados por este usuario.
     *
     * Relación uno-a-muchos con el modelo Message.
     * Representa todos los mensajes donde este usuario es el remitente.
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Obtiene las instancias de WhatsApp vinculadas a este usuario.
     */
    public function whatsappInstances(): HasMany
    {
        return $this->hasMany(WhatsappInstance::class);
    }
}

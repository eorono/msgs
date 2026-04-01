<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'instance_id',
        'token',
        'status',
        'user_id',
    ];

    /**
     * Obtiene el usuario propietario de la instancia.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los mensajes enviados desde esta instancia.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

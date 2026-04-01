<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para crear la tabla de mensajes.
 *
 * Crea la tabla 'messages' que almacena los mensajes enviados a través
 * de las diferentes plataformas de mensajería. Incluye referencias
 * al usuario remitente (user_id) y al destinatario (recipient_id).
 *
 * Estructura de la tabla:
 * - id: Clave primaria autoincremental
 * - platform: Plataforma de envío (discord, slack, telegram, whatsapp) - máx 20 caracteres
 * - message: Contenido del mensaje (texto)
 * - status: Estado del envío (sent, failed) - máx 20 caracteres
 * - user_id: Clave foránea al usuario remitente
 * - recipient_id: Clave foránea al usuario destinatario
 * - timestamps: created_at y updated_at
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración (crea la tabla messages).
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 20);
            $table->text('message');
            $table->string('status', 20);
            $table->foreignIdFor(\App\Models\User::class)->constrained();
            $table->foreignId('recipient_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración (elimina la tabla messages).
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

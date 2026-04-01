<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para agregar el campo telegram_chat_id a la tabla de usuarios.
 *
 * Agrega una columna 'telegram_chat_id' (string, nullable, unique) a la tabla
 * 'users' para almacenar el identificador del chat de Telegram de cada usuario.
 * Este campo es utilizado por TelegramService para enviar mensajes a través de la API de Telegram.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración (agrega la columna telegram_chat_id).
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable()->unique();
        });
    }

    /**
     * Revierte la migración (elimina la columna telegram_chat_id).
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telegram_chat_id');
        });
    }
};

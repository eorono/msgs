<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para agregar el campo whatsapp_number a la tabla de usuarios.
 *
 * Agrega una columna 'whatsapp_number' (string, nullable, unique) a la tabla
 * 'users' para almacenar el número de WhatsApp de cada usuario.
 * Este campo es utilizado por WhatsappService para enviar mensajes a través de la Evolution API.
 * El número se almacena limpio (solo dígitos, sin caracteres especiales).
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración (agrega la columna whatsapp_number).
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->unique();
        });
    }

    /**
     * Revierte la migración (elimina la columna whatsapp_number).
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
    }
};

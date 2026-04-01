<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder principal de la base de datos.
 *
 * Pobla la base de datos con datos iniciales para desarrollo y pruebas:
 * - 10 usuarios aleatorios generados con UserFactory
 * - 1 usuario administrador (admin@example.com / admin)
 * - 10 mensajes por cada usuario con destinatarios aleatorios
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de la base de datos.
     *
     * Crea usuarios de prueba, un usuario administrador y mensajes
     * aleatorios entre usuarios para facilitar el desarrollo y testing.
     *
     * @return void
     */
    public function run(): void
    {
        // Crear 10 usuarios de prueba
        User::factory(10)->create();

        // Crear usuario administrador con credenciales conocidas
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
        ]);

        // Crear 10 mensajes enviados para cada usuario con destinatarios aleatorios
        User::all()->each(
            function (User $user) {
                Message::factory()->count(10)->create([
                    'user_id' => $user->id,
                    'recipient_id' => fn() => User::where('id', '<>', $user->id)
                        ->inRandomOrder()->first()->id,
                ]);
            }
        );
    }
}

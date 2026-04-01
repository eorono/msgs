<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Factory para la generación de datos de prueba del modelo Message.
 *
 * Crea registros de mensajes con datos aleatorios: plataforma seleccionada
 * de las disponibles en la configuración, contenido generado por Faker,
 * estado aleatorio (sent/failed) y usuarios remitente y destinatario creados
 * automáticamente.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    use WithFaker;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => fn() => $this->faker->randomElement(array_keys(config('platforms'))),
            'message' => fn() => $this->faker->sentences(1, true),
            'status' => fn() => $this->faker->randomElement(['sent', 'failed']),
            'user_id' => fn() => User::factory()->create()->id,
            'recipient_id' => fn() => User::factory()->create()->id,
        ];
    }
}

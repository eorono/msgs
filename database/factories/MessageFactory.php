<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    use WithFaker;

    /**
     * Define the model's default state.
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

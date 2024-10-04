<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
        ]);

        // create 10 sent messages for each user
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

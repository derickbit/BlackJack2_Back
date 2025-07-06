<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partida>
 */
class PartidaFactory extends Factory
{

    public function definition(): array
    {
        return [
            'jogador' => User::factory(), // Gera um usuário para o Jogador
            'jogo' => $this->faker->randomElement(['HiLo', 'BlackJack']),
            'pontuacao' => $this->faker->numberBetween(0, 13),
        ];
    }


}

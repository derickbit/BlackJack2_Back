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
            'jogador1_id' => User::factory(), // Gera um usuário para o Jogador 1
            'jogador2_id' => User::factory(), // Gera um usuário para o Jogador 2
            'vencedor_id' => User::factory(), // Gera um usuário para o Vencedor
            'pontuacao' => $this->faker->numberBetween(0, 100),
        ];
    }


}
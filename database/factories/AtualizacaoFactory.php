<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Atualizacao>
 */
class AtualizacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipos = ['feature', 'bugfix', 'improvement', 'breaking'];

        return [
            'titulo' => $this->faker->sentence(6, true),
            'conteudo' => $this->faker->paragraphs(3, true),
            'imagem' => $this->faker->boolean(30) ? 'atualizacoes/exemplo.jpg' : null, // 30% de chance de ter imagem
            'versao' => 'v' . $this->faker->numberBetween(1, 5) . '.' . $this->faker->numberBetween(0, 9) . '.' . $this->faker->numberBetween(0, 9),
            'tipo' => $this->faker->randomElement($tipos),
            'ativo' => $this->faker->boolean(85), // 85% de chance de estar ativo
            'user_id' => User::where('role', 'admin')->inRandomOrder()->first()?->id ?? User::factory()->create(['role' => 'admin'])->id,
        ];
    }

    /**
     * Indicate that the update should be active.
     */
    public function ativa(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => true,
        ]);
    }

    /**
     * Indicate that the update should be inactive.
     */
    public function inativa(): static
    {
        return $this->state(fn (array $attributes) => [
            'ativo' => false,
        ]);
    }

    /**
     * Create a feature update.
     */
    public function feature(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'feature',
            'titulo' => 'Nova Funcionalidade: ' . $this->faker->words(3, true),
        ]);
    }

    /**
     * Create a bugfix update.
     */
    public function bugfix(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'bugfix',
            'titulo' => 'Correção: ' . $this->faker->words(3, true),
        ]);
    }
}

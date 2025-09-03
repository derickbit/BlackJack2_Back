<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumTopic>
 */
class ForumTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'titulo' => $this->faker->sentence(5),
            'conteudo' => $this->faker->paragraphs(3, true),
            'imagem' => null,
            'fixado' => $this->faker->boolean(10), // 10% chance de ser fixado
            'fechado' => $this->faker->boolean(5), // 5% chance de estar fechado
            'visualizacoes' => $this->faker->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the topic is pinned.
     */
    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'fixado' => true,
        ]);
    }

    /**
     * Indicate that the topic is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'fechado' => true,
        ]);
    }
}

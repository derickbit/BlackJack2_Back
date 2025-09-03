<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumComment>
 */
class ForumCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'forum_topic_id' => \App\Models\ForumTopic::factory(),
            'user_id' => \App\Models\User::factory(),
            'parent_id' => null,
            'conteudo' => $this->faker->paragraphs(2, true),
            'imagem' => null,
            'editado' => $this->faker->boolean(15), // 15% chance de ter sido editado
        ];
    }

    /**
     * Indicate that the comment is a reply to another comment.
     */
    public function reply(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => \App\Models\ForumComment::factory(),
        ]);
    }

    /**
     * Indicate that the comment was edited.
     */
    public function edited(): static
    {
        return $this->state(fn (array $attributes) => [
            'editado' => true,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ForumTopic;
use App\Models\ForumComment;
use App\Models\ForumLike;
use App\Models\ForumMention;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar alguns usuários se não existirem
        $users = User::take(5)->get();

        if ($users->count() < 5) {
            $users = User::factory(5)->create();
        }

        // Criar tópicos do fórum
        $topics = collect();

        // Tópico fixado
        $fixedTopic = ForumTopic::factory()->create([
            'user_id' => $users->first()->id,
            'titulo' => 'Bem-vindos ao Fórum do BlackJack!',
            'conteudo' => 'Este é o fórum oficial do jogo BlackJack. Aqui vocês podem discutir estratégias, tirar dúvidas e conversar com outros jogadores. Sejam respeitosos e divirtam-se!',
            'fixado' => true,
        ]);
        $topics->push($fixedTopic);

        // Tópicos normais
        $normalTopics = ForumTopic::factory(10)->create([
            'user_id' => fn() => $users->random()->id,
        ]);
        $topics = $topics->merge($normalTopics);

        // Criar comentários para cada tópico
        $topics->each(function ($topic) use ($users) {
            // Comentários principais
            $comments = ForumComment::factory(rand(2, 8))->create([
                'forum_topic_id' => $topic->id,
                'user_id' => fn() => $users->random()->id,
                'parent_id' => null,
            ]);

            // Algumas respostas aos comentários
            $comments->take(rand(1, 3))->each(function ($comment) use ($users, $topic) {
                ForumComment::factory(rand(1, 3))->create([
                    'forum_topic_id' => $topic->id,
                    'user_id' => fn() => $users->random()->id,
                    'parent_id' => $comment->id,
                ]);
            });
        });

        // Criar alguns likes aleatórios
        $allComments = ForumComment::all();
        $topics->each(function ($topic) use ($users) {
            $users->random(rand(1, 4))->each(function ($user) use ($topic) {
                ForumLike::create([
                    'user_id' => $user->id,
                    'likeable_type' => ForumTopic::class,
                    'likeable_id' => $topic->id,
                ]);
            });
        });

        $allComments->random(min(20, $allComments->count()))->each(function ($comment) use ($users) {
            $users->random(rand(1, 3))->each(function ($user) use ($comment) {
                try {
                    ForumLike::create([
                        'user_id' => $user->id,
                        'likeable_type' => ForumComment::class,
                        'likeable_id' => $comment->id,
                    ]);
                } catch (\Exception $e) {
                    // Ignorar duplicatas
                }
            });
        });

        $this->command->info('Fórum populado com sucesso!');
    }
}

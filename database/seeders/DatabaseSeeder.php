<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Report;
use App\Models\ReportMessage;
use App\Models\Partida;
use App\Models\ForumTopic;
use App\Models\ForumComment;
use App\Models\ForumLike;
use App\Models\Atualizacao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Os dados de demonstração não podem ser criados em produção.');
        }

        // Criar usuários básicos (reduzido de 4 para 3)
        $users = User::factory(3)->create();

        // Criar admin
        $admin = User::create([
            'name' => 'ADMIN',
            'email' => 'blackjacktcc@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make(\Illuminate\Support\Str::random(64)),
            'role' => 'admin',
        ]);

        // Dados básicos (reduzidos)
        Report::factory(2)->create();
        ReportMessage::factory(3)->create();
        Partida::factory(3)->create();

        // === FORUM - Dados Mínimos ===
        // 1 tópico fixado + 2 tópicos normais
        $fixedTopic = ForumTopic::factory()->create([
            'user_id' => $admin->id,
            'titulo' => 'Bem-vindos ao Fórum do BlackJack!',
            'conteudo' => 'Este é o fórum oficial. Discutam estratégias e divirtam-se!',
            'fixado' => true,
        ]);

        $topics = ForumTopic::factory(2)->create([
            'user_id' => fn() => $users->random()->id,
        ]);
        $allTopics = collect([$fixedTopic])->merge($topics);

        // 1-2 comentários por tópico
        $allTopics->each(function ($topic) use ($users) {
            ForumComment::factory(rand(1, 2))->create([
                'forum_topic_id' => $topic->id,
                'user_id' => fn() => $users->random()->id,
                'parent_id' => null,
            ]);
        });

        // Poucos likes
        $allTopics->take(2)->each(function ($topic) use ($users) {
            ForumLike::create([
                'user_id' => $users->random()->id,
                'likeable_type' => ForumTopic::class,
                'likeable_id' => $topic->id,
            ]);
        });

        // === ATUALIZAÇÕES - Dados Mínimos ===
        $atualizacoes = [
            [
                'titulo' => 'Sistema de Atualizações Implementado',
                'conteudo' => 'Agora você pode acompanhar todas as novidades do BlackJack!',
                'versao' => 'v1.0.0',
                'tipo' => 'feature',
                'ativo' => true,
                'user_id' => $admin->id,
            ],
            [
                'titulo' => 'Correção no Sistema de Login',
                'conteudo' => 'Corrigido problema no login após verificação de email.',
                'versao' => 'v0.9.5',
                'tipo' => 'bugfix',
                'ativo' => true,
                'user_id' => $admin->id,
            ]
        ];

        foreach ($atualizacoes as $atualizacao) {
            Atualizacao::create($atualizacao);
        }

        $this->command->info('Database seeded com dados mínimos para teste!');
    }
}

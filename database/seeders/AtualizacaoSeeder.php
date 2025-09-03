<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Atualizacao;
use App\Models\User;

class AtualizacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o primeiro usuário admin ou criar se não existir
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrador',
                'email' => 'admin@blackjack.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        // Criar atualizações de exemplo
        $atualizacoes = [
            [
                'titulo' => 'Sistema de Atualizações Implementado',
                'conteudo' => 'Agora você pode acompanhar todas as novidades e melhorias do BlackJack através do nosso novo sistema de patch notes! Os administradores poderão postar atualizações sobre desenvolvimento, correções de bugs e novas funcionalidades.',
                'versao' => 'v1.0.0',
                'tipo' => 'feature',
                'ativo' => true,
                'user_id' => $admin->id,
            ],
            [
                'titulo' => 'Correção no Sistema de Login',
                'conteudo' => 'Corrigido problema onde alguns usuários não conseguiam fazer login após a verificação de email. O sistema agora funciona corretamente para todos os usuários.',
                'versao' => 'v0.9.5',
                'tipo' => 'bugfix',
                'ativo' => true,
                'user_id' => $admin->id,
            ],
            [
                'titulo' => 'Melhoria na Performance das Partidas',
                'conteudo' => 'Otimizamos o algoritmo de simulação das partidas, resultando em uma experiência mais fluida e rápida para os jogadores. O tempo de resposta foi reduzido em aproximadamente 30%.',
                'versao' => 'v0.9.4',
                'tipo' => 'improvement',
                'ativo' => true,
                'user_id' => $admin->id,
            ],
            [
                'titulo' => 'Nova Interface de Usuário',
                'conteudo' => 'Implementamos uma nova interface mais moderna e intuitiva. Esta atualização inclui melhorias significativas na usabilidade e design responsivo para dispositivos móveis.',
                'versao' => 'v0.9.0',
                'tipo' => 'feature',
                'ativo' => true,
                'user_id' => $admin->id,
            ]
        ];

        foreach ($atualizacoes as $atualizacao) {
            Atualizacao::create($atualizacao);
        }
    }
}

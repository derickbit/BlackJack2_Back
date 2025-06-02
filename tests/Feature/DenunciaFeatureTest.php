<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DenunciaFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function testDenunciaCreationRoute()
    {
        // Cria um usuário e autentica
        $user = User::factory()->create();

        $payload = [
            'denunciante_id' => $user->id,
            'denunciado_id' => User::factory()->create()->id,
            'descricao' => 'Denúncia funcional.',
        ];

        // Adicione autenticação usando Sanctum ou outro método
        $response = $this->actingAs($user)->postJson('/api/denuncias', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('denuncia', [
            'descricao' => 'Denúncia funcional.',
        ]);
    }

}

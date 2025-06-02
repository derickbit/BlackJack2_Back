<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Denuncia;

class DenunciaTest extends TestCase
{
    public function testCreateDenuncia()
    {
        $user = User::factory()->create();

        $denuncia = Denuncia::create([
            'denunciante_id' => $user->id,
            'denunciado_id' => User::factory()->create()->id,
            'descricao' => 'Teste de denúncia.',
        ]);

        $this->assertDatabaseHas('denuncia', [
            'denunciante_id' => $user->id,
            'descricao' => 'Teste de denúncia.',
        ]);
    }
}

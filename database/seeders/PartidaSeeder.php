<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partida;

class PartidaSeeder extends Seeder
{
    public function run()
    {
        \DB::table('partidas')->truncate(); // Limpa a tabela
        Partida::factory()->count(30)->create(); // Cria 30 partidas aleatórias
    }
}

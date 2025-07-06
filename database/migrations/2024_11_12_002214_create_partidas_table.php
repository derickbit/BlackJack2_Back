<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id('codpartida');
            $table->foreignId('jogador')->constrained('users')->onDelete('cascade');
                        $table->string('jogo');
            $table->integer('pontuacao');
            $table->timestamps();
        });

    } //'Jogador1','Jogador2', 'Vencedor', 'Pontuacao'

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};

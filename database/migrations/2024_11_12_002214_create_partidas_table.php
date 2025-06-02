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
            $table->foreignId('jogador1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jogador2_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vencedor_id')->nullable()->constrained('users')->onDelete('cascade'); // Vencedor pode ser nulo inicialmente
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

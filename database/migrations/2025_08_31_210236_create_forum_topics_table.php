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
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Quem criou o tópico
            $table->string('titulo'); // Título do tópico
            $table->text('conteudo'); // Conteúdo/descrição do tópico
            $table->string('imagem')->nullable(); // Imagem opcional do tópico
            $table->boolean('fixado')->default(false); // Se o tópico está fixado (pinned)
            $table->boolean('fechado')->default(false); // Se o tópico está fechado para novos comentários
            $table->integer('visualizacoes')->default(0); // Contador de visualizações
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_topics');
    }
};

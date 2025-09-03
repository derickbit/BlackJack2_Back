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
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_topic_id')->constrained('forum_topics')->onDelete('cascade'); // Tópico ao qual o comentário pertence
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Quem fez o comentário
            $table->foreignId('parent_id')->nullable()->constrained('forum_comments')->onDelete('cascade'); // Para comentários aninhados (respostas)
            $table->text('conteudo'); // Conteúdo do comentário
            $table->string('imagem')->nullable(); // Imagem opcional do comentário
            $table->boolean('editado')->default(false); // Se o comentário foi editado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_comments');
    }
};

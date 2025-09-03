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
        Schema::create('forum_mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuário mencionado
            $table->foreignId('mentioned_by_user_id')->constrained('users')->onDelete('cascade'); // Quem fez a menção
            $table->string('mentionable_type'); // Tipo do modelo (ForumTopic ou ForumComment)
            $table->unsignedBigInteger('mentionable_id'); // ID do tópico ou comentário onde foi mencionado
            $table->boolean('lida')->default(false); // Se a menção foi lida pelo usuário
            $table->timestamps();

            // Índices para performance
            $table->index(['user_id', 'lida']);
            $table->index(['mentionable_type', 'mentionable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_mentions');
    }
};

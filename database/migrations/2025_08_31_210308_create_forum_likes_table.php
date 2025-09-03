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
        Schema::create('forum_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Quem deu o like
            $table->string('likeable_type'); // Tipo do modelo (ForumTopic ou ForumComment)
            $table->unsignedBigInteger('likeable_id'); // ID do tópico ou comentário
            $table->timestamps();

            // Índice para performance e garantir que um usuário só pode dar um like por item
            $table->unique(['user_id', 'likeable_type', 'likeable_id']);
            $table->index(['likeable_type', 'likeable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_likes');
    }
};

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
        Schema::create('atualizacaos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('conteudo');
            $table->string('versao')->nullable(); // Ex: v1.2.3
            $table->enum('tipo', ['feature', 'bugfix', 'improvement', 'breaking'])->default('improvement');
            $table->boolean('ativo')->default(true);
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Autor da atualização
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atualizacaos');
    }
};

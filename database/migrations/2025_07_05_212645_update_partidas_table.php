<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('partidas', function (Blueprint $table) {
            // Remover foreign keys antes de remover as colunas
            if (Schema::hasColumn('partidas', 'jogador2_id')) {
                $table->dropForeign(['jogador2_id']);
            }
            if (Schema::hasColumn('partidas', 'vencedor_id')) {
                $table->dropForeign(['vencedor_id']);
            }

            // Remover as colunas antigas
            if (Schema::hasColumn('partidas', 'jogador2_id')) {
                $table->dropColumn('jogador2_id');
            }
            if (Schema::hasColumn('partidas', 'vencedor_id')) {
                $table->dropColumn('vencedor_id');
            }

            // Adicionar a coluna 'jogo' se não existir
            if (!Schema::hasColumn('partidas', 'jogo')) {
                $table->string('jogo', 255)->after('jogador')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('partidas', function (Blueprint $table) {
            // Adicionar de volta as colunas removidas
            $table->unsignedBigInteger('jogador2_id')->nullable();
            $table->unsignedBigInteger('vencedor_id')->nullable();

            // Adicionar as foreign keys de volta
            $table->foreign('jogador2_id')->references('id')->on('users');
            $table->foreign('vencedor_id')->references('id')->on('users');

            // Remover a coluna 'jogo'
            $table->dropColumn('jogo');
        });
    }
};

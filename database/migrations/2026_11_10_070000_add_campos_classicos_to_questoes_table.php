<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questoes', function (Blueprint $table) {
            $table->unsignedInteger('codigo_classico')->nullable()->index()->after('id');
            $table->string('referencia_biblica')->nullable()->after('contexto');
            $table->text('ajudas')->nullable()->after('referencia_biblica');
        });

        Schema::table('jogos', function (Blueprint $table) {
            $table->longText('dados_motor')->nullable()->after('pontuacao_total');
        });
    }

    public function down(): void
    {
        Schema::table('questoes', function (Blueprint $table) {
            $table->dropColumn(['codigo_classico', 'referencia_biblica', 'ajudas']);
        });

        Schema::table('jogos', function (Blueprint $table) {
            $table->dropColumn('dados_motor');
        });
    }
};


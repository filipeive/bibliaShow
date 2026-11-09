<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jogo_id')->constrained()->onDelete('cascade');
            $table->foreignId('questao_id')->constrained('questoes')->onDelete('cascade');
            $table->foreignId('participante_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipe_id')->nullable()->constrained()->onDelete('set null');
            $table->string('resposta');
            $table->boolean('correta')->default(false);
            $table->integer('tempo_resposta')->default(0);
            $table->integer('pontos_ganhos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respostas');
    }
};

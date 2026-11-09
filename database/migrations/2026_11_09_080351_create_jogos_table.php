<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jogos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id')->constrained()->onDelete('cascade');
            $table->enum('estado', ['aguardando', 'em_curso', 'pausado', 'terminado'])->default('aguardando');
            $table->foreignId('pergunta_atual_id')->nullable()->constrained('questoes')->nullOnDelete();
            $table->integer('pontuacao_total')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jogos');
    }
};

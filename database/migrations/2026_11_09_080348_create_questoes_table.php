<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igreja_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained('livros_biblicos');
            $table->unsignedBigInteger('capitulo_id')->nullable();
            $table->integer('versiculo_de')->nullable();
            $table->integer('versiculo_ate')->nullable();
            $table->text('texto');
            $table->text('opcoes')->nullable();
            $table->string('resposta_correta');
            $table->text('explicacao')->nullable();
            $table->text('contexto')->nullable();
            $table->foreignId('dificuldade_id')->nullable()->constrained('niveis_dificuldade')->nullOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->boolean('referencia_verificada')->default(false);
            $table->boolean('aprovada')->default(false);
            $table->foreignId('submetida_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('revisada_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('revisada_em')->nullable();
            $table->text('notas')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questoes');
    }
};

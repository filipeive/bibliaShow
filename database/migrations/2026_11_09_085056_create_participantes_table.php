<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipe_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nome');
            $table->string('codigo_acesso', 10)->nullable();
            $table->boolean('conectado')->default(true);
            $table->integer('pontuacao_total')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participantes');
    }
};

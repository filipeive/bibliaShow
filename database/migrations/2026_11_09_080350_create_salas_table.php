<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('igreja_id')->constrained()->onDelete('cascade');
            $table->string('codigo_acesso')->unique();
            $table->string('nome');
            $table->foreignId('apresentador_id')->constrained('usuarios')->onDelete('cascade');
            $table->enum('status', ['aguardando', 'em_curso', 'pausada', 'terminada'])->default('aguardando');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salas');
    }
};

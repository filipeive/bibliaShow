<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livros_biblicos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_osis', 16)->unique();
            $table->string('abreviacao', 16);
            $table->string('nome');
            $table->string('testamento');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livros_biblicos');
    }
};
